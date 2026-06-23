<?php
namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\IncomingGood;
use App\Models\InventoryAnalysis;
use App\Models\OutgoingGood;
use App\Models\OutgoingGoodDetail;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBatch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Services\LogActivity;

class ReportController extends Controller
{
    /**
     * Helper to get simulated product pricing and brand.
     */
    private function getProductPricing($product)
    {
        $name = strtolower($product->nama_produk);
        
        // Default fallback
        $hargaJual = 25000;
        $hargaBeli = 16000;
        $brand = 'Della';
        
        if (str_contains($name, 'cireng')) {
            $hargaJual = 25000;
            $hargaBeli = 16000;
            $brand = 'Della';
        } elseif (str_contains($name, 'sosis ayam')) {
            $hargaJual = 30000;
            $hargaBeli = 18000;
            $brand = 'Champ';
        } elseif (str_contains($name, 'nugget')) {
            $hargaJual = 28000;
            $hargaBeli = 17000;
            $brand = 'Fiesta';
        } elseif (str_contains($name, 'bakso sapi')) {
            $hargaJual = 32000;
            $hargaBeli = 18000;
            $brand = 'Della';
        } elseif (str_contains($name, 'kentang')) {
            $hargaJual = 24000;
            $hargaBeli = 14000;
            $brand = 'Lambweston';
        } elseif (str_contains($name, 'daging ayam')) {
            $hargaJual = 35000;
            $hargaBeli = 23000;
            $brand = 'Della';
        } elseif (str_contains($name, 'tempura') || str_contains($name, 'udang')) {
            $hargaJual = 45000;
            $hargaBeli = 30000;
            $brand = 'Ebi Fry';
        } elseif (str_contains($name, 'otak-otak') || str_contains($name, 'otak')) {
            $hargaJual = 22000;
            $hargaBeli = 14000;
            $brand = 'Della';
        } elseif (str_contains($name, 'karage') || str_contains($name, 'karaage')) {
            $hargaJual = 38000;
            $hargaBeli = 23000;
            $brand = 'Karaage-ku';
        } elseif (str_contains($name, 'cocktail')) {
            $hargaJual = 26000;
            $hargaBeli = 19428;
            $brand = 'Champ';
        } else {
            // Dynamic based on database average purchase price
            $avgBeli = IncomingGood::where('product_id', $product->id)->avg('harga_beli');
            if ($avgBeli > 0) {
                $hargaBeli = (float)$avgBeli;
                $hargaJual = round($hargaBeli * 1.4, -3); // 40% markup, rounded to thousands
            }
        }
        
        return [
            'harga_jual' => $hargaJual,
            'harga_beli' => $hargaBeli,
            'brand' => $brand
        ];
    }

    /**
     * Get processed sales list grouped by product for reports.
     */
    private function getProcessedSalesData(Request $request)
    {
        $query = Sale::with('product.category');
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_penjualan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_penjualan', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $salesData = $query->selectRaw('product_id, SUM(jumlah_terjual) as total_qty')
            ->groupBy('product_id')
            ->get();

        $processedSales = collect();

        foreach ($salesData as $item) {
            $product = $item->product;
            if (!$product) continue;

            $pricing = $this->getProductPricing($product);
            
            $qty = (int)$item->total_qty;
            $omzet = $qty * $pricing['harga_jual'];
            $modal = $qty * $pricing['harga_beli'];
            $laba = $omzet - $modal;

            $processedSales->push([
                'product' => $product,
                'nama_produk' => $product->nama_produk,
                'kode_produk' => $product->kode_produk,
                'kategori' => $product->category->nama_kategori ?? '-',
                'brand' => $pricing['brand'],
                'harga_jual' => $pricing['harga_jual'],
                'total_qty' => $qty,
                'total_omzet' => $omzet,
                'laba_kotor' => $laba
            ]);
        }

        return $processedSales->sortByDesc('total_omzet')->values();
    }

    public function penjualan(Request $request)
    {
        $processedSales = $this->getProcessedSalesData($request);

        // Calculate totals
        $totalOmzet = $processedSales->sum('total_omzet');
        $totalLabaKotor = $processedSales->sum('laba_kotor');
        $totalQty = $processedSales->sum('total_qty');
        $totalProdukTerjual = $processedSales->count();
        $margin = $totalOmzet > 0 ? ($totalLabaKotor / $totalOmzet) * 100 : 0;

        $products = Product::orderBy('nama_produk')->get();

        return view('reports.penjualan', compact(
            'processedSales', 
            'products', 
            'totalOmzet', 
            'totalLabaKotor', 
            'totalQty', 
            'totalProdukTerjual',
            'margin'
        ));
    }

    public function persediaan(Request $request)
    {
        $query = InventoryAnalysis::with('product.category')
            ->whereIn('id', function($sub) {
                $sub->selectRaw('MAX(id)')->from('analisa_persediaan')->groupBy('product_id');
            });
            
        if ($request->filled('status_stok')) {
            $query->where('status_stok', $request->status_stok);
        }

        $analyses = $query->get();
        return view('reports.persediaan', compact('analyses'));
    }

    public function barangMasuk(Request $request)
    {
        $query = IncomingGood::with(['product.category', 'supplier']);
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Get all matching data for totals
        $allIncoming = $query->latest('tanggal_masuk')->get();

        $totalTransaksi = $allIncoming->count();
        $totalQty = $allIncoming->sum('jumlah');
        
        $totalNilai = 0;
        foreach ($allIncoming as $item) {
            $totalNilai += $item->jumlah * ($item->harga_beli ?? 0);
        }

        $totalProduk = $allIncoming->pluck('product_id')->unique()->count();

        // Paginate
        $page = $request->query('page', 1);
        $perPage = 15;
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $allIncoming->forPage($page, $perPage),
            $allIncoming->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $products = Product::orderBy('nama_produk')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('reports.barang-masuk', compact(
            'paginatedItems',
            'products',
            'suppliers',
            'totalTransaksi',
            'totalQty',
            'totalNilai',
            'totalProduk'
        ));
    }

    /**
     * Get processed outgoing goods with FIFO calculations.
     */
    private function getProcessedOutgoingData(Request $request)
    {
        $query = OutgoingGood::with(['product.incomingGoods', 'outgoingGoodDetails.stockBatch.incomingGood']);
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $allOutgoing = $query->latest('tanggal_keluar')->get();

        $processedOutgoing = collect();

        foreach ($allOutgoing as $item) {
            $product = $item->product;
            if (!$product) continue;

            $value = 0;
            $batchDates = collect();
            
            if ($item->outgoingGoodDetails->count() > 0) {
                foreach ($item->outgoingGoodDetails as $detail) {
                    $hargaBeli = $detail->stockBatch->incomingGood->harga_beli ?? 0;
                    if ($hargaBeli == 0) {
                        $hargaBeli = $product->incomingGoods->avg('harga_beli') ?? 16000;
                    }
                    $value += $detail->jumlah_diambil * $hargaBeli;
                    
                    if ($detail->stockBatch->tanggal_masuk) {
                        $batchDates->push($detail->stockBatch->tanggal_masuk->translatedFormat('d M Y'));
                    }
                }
            } else {
                $hargaBeli = $product->incomingGoods->avg('harga_beli') ?? 16000;
                $value = $item->jumlah * $hargaBeli;
            }

            $batchDatesString = $batchDates->unique()->implode(', ');
            if (empty($batchDatesString)) {
                $batchDatesString = '-';
            }

            // Prepare FIFO details for interactive dashboard panel
            $fifoDetails = [
                'transaction_code' => 'BK' . $item->created_at->format('ymd') . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'product_name' => $product->nama_produk,
                'qty_keluar' => $item->jumlah,
                'nilai_keluar_formatted' => 'Rp ' . number_format($value, 0, ',', '.'),
                'batches_used' => $item->outgoingGoodDetails->map(function($detail) {
                    return [
                        'batch_code' => $detail->stockBatch->batch_code ?? '-',
                        'tanggal_masuk' => $detail->stockBatch->tanggal_masuk ? $detail->stockBatch->tanggal_masuk->translatedFormat('d M Y') : '-',
                        'qty_terpakai' => $detail->jumlah_diambil,
                        'sisa_setelah_dipakai' => $detail->stockBatch->jumlah_sisa
                    ];
                }),
                'available_batches' => StockBatch::where('product_id', $product->id)
                    ->where('tanggal_masuk', '<=', $item->tanggal_keluar)
                    ->orderBy('tanggal_masuk', 'asc')
                    ->get()
                    ->map(function($batch) use ($item) {
                        $consumedBefore = OutgoingGoodDetail::where('stock_batch_id', $batch->id)
                            ->where('created_at', '<', $item->created_at)
                            ->sum('jumlah_diambil');
                        
                        return [
                            'tanggal_masuk' => $batch->tanggal_masuk ? $batch->tanggal_masuk->translatedFormat('d M Y') : '-',
                            'batch_code' => $batch->batch_code ?? '-',
                            'qty_masuk' => $batch->jumlah_awal,
                            'sisa_sebelum' => $batch->jumlah_awal - $consumedBefore
                        ];
                    })
            ];

            $processedOutgoing->push([
                'id' => $item->id,
                'tanggal_keluar' => $item->tanggal_keluar,
                'transaction_code' => 'BK' . $item->created_at->format('ymd') . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'product' => $product,
                'nama_produk' => $product->nama_produk,
                'kode_produk' => $product->kode_produk,
                'jumlah' => $item->jumlah,
                'jenis_keluar' => $item->jenis_keluar,
                'keterangan' => $item->keterangan,
                'tanggal_barang_masuk' => $batchDatesString,
                'nilai' => $value,
                'fifo_details' => $fifoDetails
            ]);
        }

        return $processedOutgoing;
    }

    public function barangKeluar(Request $request)
    {
        $processedOutgoing = $this->getProcessedOutgoingData($request);

        $totalQty = $processedOutgoing->sum('jumlah');
        $totalNilai = $processedOutgoing->sum('nilai');
        $totalProduk = $processedOutgoing->pluck('product.id')->unique()->count();
        $totalTransaksi = $processedOutgoing->count();

        // Paginate manually
        $page = $request->query('page', 1);
        $perPage = 10;
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedOutgoing->forPage($page, $perPage),
            $processedOutgoing->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $products = Product::orderBy('nama_produk')->get();

        return view('reports.barang-keluar', compact(
            'paginatedItems',
            'products',
            'totalQty',
            'totalNilai',
            'totalProduk',
            'totalTransaksi'
        ));
    }

    public function importLog(Request $request)
    {
        $query = ImportLog::with('user');
        
        if ($request->filled('jenis_import')) {
            $query->where('jenis_import', $request->jenis_import);
        }
        
        $logs = $query->latest()->paginate(20)->withQueryString();
        return view('reports.import-log', compact('logs'));
    }

    /**
     * Excel Export for Sales (CSV stream openable in Excel).
     */
    public function exportPenjualanExcel(Request $request)
    {
        $processedSales = $this->getProcessedSalesData($request);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        
        $role = auth()->user()->role ?? 'admin';
        $isOwner = ($role === 'owner');

        LogActivity::log('export', 'Export Excel Laporan Penjualan', "Mengekspor Laporan Penjualan ke Excel (CSV) untuk Periode: {$periode}.");

        $filename = "laporan_penjualan_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        if ($isOwner) {
            $columns = ['NO', 'NAMA PRODUK', 'KATEGORI', 'BRAND', 'HARGA JUAL (RP)', 'TOTAL QTY (PCS)', 'TOTAL OMZET (RP)', 'LABA KOTOR (RP)'];
        } else {
            $columns = ['NO', 'NAMA PRODUK', 'KATEGORI', 'BRAND', 'TOTAL QTY (PCS)'];
        }

        $callback = function() use($processedSales, $columns, $isOwner) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Force Excel to use semicolon separator
            fwrite($file, "sep=;\n");
            
            fputcsv($file, $columns, ';');

            $totalQty = 0;
            $totalOmzet = 0;
            $totalLaba = 0;

            foreach ($processedSales as $index => $item) {
                $totalQty += (int)$item['total_qty'];
                $totalOmzet += (float)$item['total_omzet'];
                $totalLaba += (float)$item['laba_kotor'];

                if ($isOwner) {
                    fputcsv($file, [
                        $index + 1,
                        $item['nama_produk'],
                        $item['kategori'],
                        $item['brand'],
                        (float)$item['harga_jual'],
                        (int)$item['total_qty'],
                        (float)$item['total_omzet'],
                        (float)$item['laba_kotor']
                    ], ';');
                } else {
                    fputcsv($file, [
                        $index + 1,
                        $item['nama_produk'],
                        $item['kategori'],
                        $item['brand'],
                        (int)$item['total_qty']
                    ], ';');
                }
            }

            // Append summary footer row
            if ($isOwner) {
                fputcsv($file, [
                    'TOTAL',
                    '',
                    '',
                    '',
                    '',
                    $totalQty,
                    $totalOmzet,
                    $totalLaba
                ], ';');
            } else {
                fputcsv($file, [
                    'TOTAL',
                    '',
                    '',
                    '',
                    $totalQty
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Printable PDF layout for Sales.
     */
    public function exportPenjualanPdf(Request $request)
    {
        $processedSales = $this->getProcessedSalesData($request);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        LogActivity::log('export', 'Cetak PDF Laporan Penjualan', "Mencetak Laporan Penjualan (PDF) untuk Periode: {$periode}.");

        $totalOmzet = $processedSales->sum('total_omzet');
        $totalLabaKotor = $processedSales->sum('laba_kotor');
        $totalQty = $processedSales->sum('total_qty');
        $totalProdukTerjual = $processedSales->count();
        $margin = $totalOmzet > 0 ? ($totalLabaKotor / $totalOmzet) * 100 : 0;

        return view('reports.penjualan-print', compact(
            'processedSales', 
            'totalOmzet', 
            'totalLabaKotor', 
            'totalQty', 
            'totalProdukTerjual',
            'margin'
        ));
    }

    /**
     * Excel Export for Outgoing Goods.
     */
    public function exportBarangKeluarExcel(Request $request)
    {
        $processedOutgoing = $this->getProcessedOutgoingData($request);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        LogActivity::log('export', 'Export Excel Laporan Barang Keluar', "Mengekspor Laporan Barang Keluar ke Excel (CSV) untuk Periode: {$periode}.");

        $filename = "laporan_barang_keluar_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NO', 'TANGGAL KELUAR', 'NO. TRANSAKSI', 'NAMA PRODUK', 'QTY KELUAR (PCS)', 'TUJUAN / KETERANGAN', 'TANGGAL MASUK (FIFO)', 'TOTAL NILAI (RP)'];

        $callback = function() use($processedOutgoing, $columns) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Force Excel to use semicolon separator
            fwrite($file, "sep=;\n");
            
            fputcsv($file, $columns, ';');

            $totalQty = 0;
            $totalNilai = 0;

            foreach ($processedOutgoing as $index => $item) {
                $totalQty += (int)$item['jumlah'];
                $totalNilai += (float)$item['nilai'];

                fputcsv($file, [
                    $index + 1,
                    $item['tanggal_keluar']->translatedFormat('d M Y H:i'),
                    $item['transaction_code'],
                    $item['nama_produk'],
                    (int)$item['jumlah'],
                    $item['keterangan'] ?? ucfirst(str_replace('_', ' ', $item['jenis_keluar'])),
                    $item['tanggal_barang_masuk'],
                    (float)$item['nilai']
                ], ';');
            }

            // Append summary footer row
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                '',
                $totalQty,
                '',
                '',
                $totalNilai
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Printable PDF layout for Outgoing Goods.
     */
    public function exportBarangKeluarPdf(Request $request)
    {
        $processedOutgoing = $this->getProcessedOutgoingData($request);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        LogActivity::log('export', 'Cetak PDF Laporan Barang Keluar', "Mencetak Laporan Barang Keluar (PDF) untuk Periode: {$periode}.");

        $totalQty = $processedOutgoing->sum('jumlah');
        $totalNilai = $processedOutgoing->sum('nilai');
        $totalProduk = $processedOutgoing->pluck('product.id')->unique()->count();
        $totalTransaksi = $processedOutgoing->count();

        return view('reports.barang-keluar-print', compact(
            'processedOutgoing',
            'totalQty',
            'totalNilai',
            'totalProduk',
            'totalTransaksi'
        ));
    }

    public function exportPersediaanPdf() { return back()->with('info', 'Fitur Export PDF sedang dalam pengembangan.'); }
    public function exportPersediaanExcel() { return back()->with('info', 'Fitur Export Excel sedang dalam pengembangan.'); }
    
    /**
     * Excel Export for Incoming Goods (CSV stream).
     */
    public function exportBarangMasukExcel(Request $request)
    {
        $query = IncomingGood::with(['product.category', 'supplier']);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        LogActivity::log('export', 'Export Excel Laporan Barang Masuk', "Mengekspor Laporan Barang Masuk ke Excel (CSV) untuk Periode: {$periode}.");
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $incomingGoods = $query->latest('tanggal_masuk')->get();

        $filename = "laporan_barang_masuk_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NO', 'TANGGAL MASUK', 'KODE PRODUK', 'NAMA PRODUK', 'SUPPLIER', 'QTY MASUK (PCS)', 'HARGA BELI (RP)', 'TOTAL NILAI (RP)', 'NO. BATCH', 'LOKASI', 'KEDALUWARSA'];

        $callback = function() use($incomingGoods, $columns) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Force Excel to use semicolon separator
            fwrite($file, "sep=;\n");
            
            fputcsv($file, $columns, ';');

            $totalQty = 0;
            $totalNilai = 0;

            foreach ($incomingGoods as $index => $item) {
                $totalBaris = $item->jumlah * ($item->harga_beli ?? 0);
                $totalQty += (int)$item->jumlah;
                $totalNilai += (float)$totalBaris;

                fputcsv($file, [
                    $index + 1,
                    $item->tanggal_masuk->translatedFormat('d M Y'),
                    $item->product->kode_produk ?? '-',
                    $item->product->nama_produk ?? '-',
                    $item->supplier->nama_supplier ?? '-',
                    (int)$item->jumlah,
                    (float)$item->harga_beli,
                    (float)$totalBaris,
                    $item->batch_code ?? '-',
                    $item->id_lokasi ?? '-',
                    $item->tanggal_kedaluwarsa ? $item->tanggal_kedaluwarsa->translatedFormat('d M Y') : '-'
                ], ';');
            }

            // Append summary footer row
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                '',
                '',
                $totalQty,
                '',
                $totalNilai,
                '',
                '',
                ''
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Printable PDF layout for Incoming Goods.
     */
    public function exportBarangMasukPdf(Request $request)
    {
        $query = IncomingGood::with(['product.category', 'supplier']);

        $periode = 'Semua Periode';
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $periode = ($request->tanggal_dari ?? 'Awal') . ' s/d ' . ($request->tanggal_sampai ?? 'Kini');
        }
        LogActivity::log('export', 'Cetak PDF Laporan Barang Masuk', "Mencetak Laporan Barang Masuk (PDF) untuk Periode: {$periode}.");
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $incomingGoods = $query->latest('tanggal_masuk')->get();

        $totalTransaksi = $incomingGoods->count();
        $totalQty = $incomingGoods->sum('jumlah');
        
        $totalNilai = 0;
        foreach ($incomingGoods as $item) {
            $totalNilai += $item->jumlah * ($item->harga_beli ?? 0);
        }

        $totalProduk = $incomingGoods->pluck('product_id')->unique()->count();

        return view('reports.barang-masuk-print', compact(
            'incomingGoods',
            'totalQty',
            'totalNilai',
            'totalProduk',
            'totalTransaksi'
        ));
    }
}
