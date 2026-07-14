<?php
namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\OutgoingGood;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PenjualanImport;

class ImportPenjualanController extends Controller
{
    public function index()
    {
        $importLogs = ImportLog::where('jenis_import', 'penjualan')->latest()->take(10)->get();
        return view('imports.penjualan', compact('importLogs'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new PenjualanImport();
            Excel::import($import, $request->file('file'));
            
            $validRows = $import->getValidRows();
            $errorRows = $import->getErrorRows();
            
            // Check if product exists
            foreach($validRows as $k => $row) {
                $searchName = $row['nama_barang'];

                $product = Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($searchName)])
                    ->orWhere('kode_produk', strtoupper($searchName))
                    ->first();
                    
                if (!$product) {
                    $errorRows[] = [
                        'row_number' => '-',
                        'tanggal' => $row['tanggal'],
                        'nama_barang' => $row['nama_barang'],
                        'error' => 'Produk tidak ditemukan di database'
                    ];
                    unset($validRows[$k]);
                } else {
                    $validRows[$k]['product_id'] = $product->id;
                    $validRows[$k]['stok_saat_ini'] = $product->stok_saat_ini;
                }
            }
            
            $validRows = array_values($validRows); // reindex

            $previewData = [
                'valid_rows' => $validRows,
                'error_rows' => $errorRows,
                'total_rows' => count($validRows) + count($errorRows),
                'file_name' => $request->file('file')->getClientOriginalName()
            ];

            session(['import_penjualan_preview' => $previewData]);

            return response()->json([
                'success' => true,
                'data' => $previewData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan membaca file: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $previewData = session('import_penjualan_preview');
        
        if (!$previewData || empty($previewData['valid_rows'])) {
            return back()->withErrors(['error' => 'Data preview tidak ditemukan atau kosong. Silakan upload ulang.']);
        }

        $kurangiStok = $request->has('kurangi_stok');

        DB::beginTransaction();
        try {
            $jumlahBarisBerhasil = 0;
            
            foreach ($previewData['valid_rows'] as $row) {
                
                Sale::create([
                    'product_id' => $row['product_id'],
                    'tanggal_penjualan' => $row['tanggal'],
                    'jumlah_terjual' => $row['jumlah'],
                    'sumber_import' => 'Import Penjualan',
                    'nama_file_import' => $previewData['file_name'],
                    'user_id' => auth()->id(),
                ]);

                if ($kurangiStok) {
                    $product = Product::find($row['product_id']);
                    if ($product->stok_saat_ini >= $row['jumlah']) {
                        $outgoing = OutgoingGood::create([
                            'product_id' => $product->id,
                            'tanggal_keluar' => $row['tanggal'],
                            'jumlah' => $row['jumlah'],
                            'jenis_keluar' => 'penjualan',
                            'keterangan' => 'Dari import penjualan: ' . $previewData['file_name'],
                            'user_id' => auth()->id(),
                        ]);
                        
                        app(\App\Services\FifoService::class)->deductStock($product->id, $row['jumlah'], $outgoing->id);
                    }
                }

                $jumlahBarisBerhasil++;
            }

            ImportLog::create([
                'jenis_import' => 'penjualan',
                'nama_file' => $previewData['file_name'],
                'jumlah_baris' => $previewData['total_rows'],
                'jumlah_berhasil' => $jumlahBarisBerhasil,
                'jumlah_gagal' => count($previewData['error_rows']),
                'catatan_error' => json_encode($previewData['error_rows']),
                'user_id' => auth()->id(),
            ]);
            
            app(\App\Services\NotificationService::class)->createImportSuccess(
                'penjualan', 
                $previewData['file_name'], 
                $jumlahBarisBerhasil, 
                auth()->id()
            );

            DB::commit();
            session()->forget('import_penjualan_preview');

            \App\Services\LogActivity::log('import', 'Import Penjualan', "Mengimpor berkas data penjualan '{$previewData['file_name']}' (Berhasil: {$jumlahBarisBerhasil} baris, Gagal: " . count($previewData['error_rows']) . " baris).");

            return redirect()->route('import-penjualan.index')->with('success_import', [
                'nama_file' => $previewData['file_name'],
                'jumlah_berhasil' => $jumlahBarisBerhasil,
                'jumlah_gagal' => count($previewData['error_rows'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan import: ' . $e->getMessage()]);
        }
    }
}
