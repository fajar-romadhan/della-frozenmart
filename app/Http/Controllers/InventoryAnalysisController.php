<?php
namespace App\Http\Controllers;

use App\Models\InventoryAnalysis;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SafetyStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PenjualanImport;
use App\Services\LogActivity;

class InventoryAnalysisController extends Controller
{
    protected $safetyStockService;

    public function __construct(SafetyStockService $safetyStockService)
    {
        $this->safetyStockService = $safetyStockService;
    }

    public function index(Request $request)
    {
        $query = InventoryAnalysis::with('product');
        
        // Only get the latest analysis per product
        $query->whereIn('id', function($sub) {
            $sub->selectRaw('MAX(id)')->from('analisa_persediaan')->groupBy('product_id');
        });

        if ($request->filled('status_stok')) {
            $query->where('status_stok', $request->status_stok);
        }

        $analyses = $query->get();
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        
        return view('inventory-analysis.index', compact('analyses', 'products'));
    }

    public function analyze(Product $product)
    {
        try {
            $this->safetyStockService->calculate($product);
            LogActivity::log('opname', 'Analisis Persediaan Produk', "Memicu kalkulasi Safety Stock & ROP untuk produk '{$product->nama_produk}' ({$product->kode_produk}).");
            return redirect()->back()->with('success', 'Analisis persediaan untuk ' . $product->nama_produk . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal melakukan analisis: ' . $e->getMessage()]);
        }
    }

    public function analyzeAll()
    {
        try {
            $products = Product::where('status_aktif', true)->get();
            $count = 0;
            foreach ($products as $product) {
                $this->safetyStockService->calculate($product);
                $count++;
            }
            LogActivity::log('opname', 'Analisis Seluruh Persediaan', "Memicu kalkulasi ulang Safety Stock & ROP untuk seluruh produk aktif ({$count} produk terpengaruh).");
            return redirect()->back()->with('success', $count . ' produk berhasil dianalisis.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal melakukan analisis: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $analysis = InventoryAnalysis::where('product_id', $product->id)->latest()->first();
        if (!$analysis) {
            return redirect()->route('analisis.index')->with('warning', 'Produk ini belum pernah dianalisis.');
        }
        
        return view('inventory-analysis.show', compact('analysis', 'product'));
    }

    public function statusStok()
    {
        $query = InventoryAnalysis::with('product')
            ->whereIn('id', function($sub) {
                $sub->selectRaw('MAX(id)')->from('analisa_persediaan')->groupBy('product_id');
            });

        $analyses = $query->get();
        
        $amanCount = $analyses->where('status_stok', 'Aman')->count();
        $warningCount = $analyses->where('status_stok', 'Warning')->count();
        $orderCount = $analyses->where('status_stok', 'Order')->count();

        return view('status-stok.index', compact('analyses', 'amanCount', 'warningCount', 'orderCount'));
    }

    public function uploadSales(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            DB::beginTransaction();

            $import = new PenjualanImport();
            Excel::import($import, $request->file('file'));
            
            $validRows = $import->getValidRows();
            $jumlahBarisBerhasil = 0;
            
            foreach($validRows as $row) {
                $searchName = $row['nama_barang'];

                $product = Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($searchName)])
                    ->orWhere('kode_produk', strtoupper($searchName))
                    ->first();
                    
                if ($product) {
                    Sale::create([
                        'product_id' => $product->id,
                        'tanggal_penjualan' => $row['tanggal'],
                        'jumlah_terjual' => $row['jumlah'],
                        'sumber_import' => 'Analisa Persediaan Upload',
                        'nama_file_import' => $request->file('file')->getClientOriginalName(),
                        'user_id' => auth()->id(),
                    ]);
                    $jumlahBarisBerhasil++;
                }
            }

            // Recalculate all products
            $products = Product::where('status_aktif', true)->get();
            foreach ($products as $product) {
                $this->safetyStockService->calculate($product);
            }

            // Log import
            \App\Models\ImportLog::create([
                'jenis_import' => 'penjualan',
                'nama_file' => $request->file('file')->getClientOriginalName(),
                'jumlah_baris' => count($validRows) + count($import->getErrorRows()),
                'jumlah_berhasil' => $jumlahBarisBerhasil,
                'jumlah_gagal' => count($import->getErrorRows()),
                'catatan_error' => json_encode($import->getErrorRows()),
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            LogActivity::log('import', 'Import Data Penjualan', "Mengimpor berkas penjualan harian '{$request->file('file')->getClientOriginalName()}' (Berhasil: {$jumlahBarisBerhasil} baris).");

            return redirect()->back()->with('success', "File berhasil diunggah. $jumlahBarisBerhasil data penjualan berhasil diimport dan seluruh analisa persediaan telah diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mengunggah dan menganalisis file: ' . $e->getMessage()]);
        }
    }
}
