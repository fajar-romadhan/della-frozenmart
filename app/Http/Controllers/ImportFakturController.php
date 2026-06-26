<?php
namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\IncomingGood;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FakturPembelianImport;
use App\Services\LogActivity;

class ImportFakturController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $importLogs = ImportLog::where('jenis_import', 'faktur_pembelian')->latest()->take(10)->get();
        return view('imports.faktur-pembelian', compact('suppliers', 'importLogs'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'supplier_id' => 'nullable|exists:supplier,id'
        ]);

        try {
            $import = new FakturPembelianImport();
            Excel::import($import, $request->file('file'));
            
            $validRows = $import->getValidRows();
            $errorRows = $import->getErrorRows();
            
            // Generate product status (Baru / Existing)
            foreach($validRows as &$row) {
                $product = Product::whereRaw('LOWER(nama_produk) = ?', [strtolower(trim($row['nama_barang']))])->first();
                $row['status_produk'] = $product ? 'Existing' : 'Baru';
            }

            $previewData = [
                'valid_rows' => $validRows,
                'error_rows' => $errorRows,
                'total_rows' => count($validRows) + count($errorRows),
                'total_stok' => array_sum(array_column($validRows, 'stok_ditambahkan')),
                'supplier_id' => $request->supplier_id,
                'file_name' => $request->file('file')->getClientOriginalName()
            ];

            session(['import_faktur_preview' => $previewData]);

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
        $previewData = session('import_faktur_preview');
        
        if (!$previewData || empty($previewData['valid_rows'])) {
            return back()->withErrors(['error' => 'Data preview tidak ditemukan atau kosong. Silakan upload ulang.']);
        }

        $supplier_id = $previewData['supplier_id'];
        if (!$supplier_id) {
            $defaultSupplier = Supplier::firstOrCreate(
                ['nama_supplier' => 'Supplier Tidak Diketahui'],
                ['keterangan' => 'Supplier default untuk import tanpa supplier']
            );
            $supplier_id = $defaultSupplier->id;
        }

        $defaultCategory = \App\Models\Category::firstOrCreate(
            ['nama_kategori' => 'Frozen Food']
        );

        DB::beginTransaction();
        try {
            $jumlahBarisBerhasil = 0;
            $produkBaru = 0;
            $produkLama = 0;
            
            foreach ($previewData['valid_rows'] as $row) {
                $namaBarang = trim($row['nama_barang']);
                
                $product = Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($namaBarang)])->first();
                
                if (!$product) {
                    $product = Product::create([
                        'kode_produk' => Product::generateKodeProduk(),
                        'nama_produk' => $namaBarang,
                        'category_id' => $defaultCategory->id,
                        'satuan' => $row['satuan'],
                        'stok_saat_ini' => 0,
                    ]);
                    $produkBaru++;
                } else {
                    $produkLama++;
                }

                $tanggal = Carbon::createFromFormat('d/m/Y', $row['tanggal'])->format('Y-m-d');
                $dateStr = str_replace('-', '', $tanggal);
                $countToday = IncomingGood::whereDate('tanggal_masuk', $tanggal)->count() + 1;
                $batchCode = 'BM-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                $incoming = IncomingGood::create([
                    'product_id' => $product->id,
                    'supplier_id' => $supplier_id,
                    'tanggal_masuk' => $tanggal,
                    'jumlah' => $row['stok_ditambahkan'],
                    'satuan' => $row['satuan'],
                    'batch_code' => $batchCode,
                    'sumber_import' => 'Faktur Pembelian',
                    'nama_file_import' => $previewData['file_name'],
                    'user_id' => auth()->id(),
                ]);

                StockBatch::create([
                    'product_id' => $product->id,
                    'incoming_good_id' => $incoming->id,
                    'batch_code' => $batchCode,
                    'tanggal_masuk' => $tanggal,
                    'jumlah_awal' => $row['stok_ditambahkan'],
                    'jumlah_sisa' => $row['stok_ditambahkan'],
                    'satuan' => $row['satuan'],
                ]);

                $product->increment('stok_saat_ini', $row['stok_ditambahkan']);
                $jumlahBarisBerhasil++;
            }

            $importLog = ImportLog::create([
                'jenis_import' => 'faktur_pembelian',
                'nama_file' => $previewData['file_name'],
                'jumlah_baris' => $previewData['total_rows'],
                'jumlah_berhasil' => $jumlahBarisBerhasil,
                'jumlah_gagal' => count($previewData['error_rows']),
                'catatan_error' => json_encode($previewData['error_rows']),
                'user_id' => auth()->id(),
            ]);
            
            app(\App\Services\NotificationService::class)->createImportSuccess(
                'faktur pembelian', 
                $previewData['file_name'], 
                $jumlahBarisBerhasil, 
                auth()->id()
            );

            DB::commit();
            session()->forget('import_faktur_preview');

            LogActivity::log('import', 'Import Faktur Pembelian', "Mengimpor berkas faktur pembelian '{$previewData['file_name']}' (Berhasil: {$jumlahBarisBerhasil} baris, Gagal: " . count($previewData['error_rows']) . " baris).");

            return redirect()->route('import-faktur.index')->with('success_import', [
                'nama_file' => $previewData['file_name'],
                'jumlah_berhasil' => $jumlahBarisBerhasil,
                'jumlah_gagal' => count($previewData['error_rows']),
                'produk_baru' => $produkBaru,
                'produk_lama' => $produkLama,
                'total_stok' => $previewData['total_stok']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan import: ' . $e->getMessage()]);
        }
    }
}
