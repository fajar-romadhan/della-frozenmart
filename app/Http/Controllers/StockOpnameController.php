<?php
namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\Product;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogActivity;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with(['product', 'user'])->latest('tanggal_opname');
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_opname', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_opname', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'sesuai') {
                $query->where('selisih', 0);
            } elseif ($request->status === 'selisih') {
                $query->where('selisih', '!=', 0);
            }
        }
        
        // Stats
        $statsQuery = StockOpname::query();
        if ($request->filled('tanggal_dari')) {
            $statsQuery->whereDate('tanggal_opname', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $statsQuery->whereDate('tanggal_opname', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $statsQuery->where('product_id', $request->product_id);
        }

        $totalOpname = $statsQuery->count();
        $totalSesuai = (clone $statsQuery)->where('selisih', 0)->count();
        $totalSelisih = (clone $statsQuery)->where('selisih', '!=', 0)->count();
        $totalPcsSelisih = (clone $statsQuery)->sum(DB::raw('ABS(selisih)'));

        $stockOpnames = $query->paginate(10)->withQueryString();
        $products = Product::orderBy('nama_produk')->get();
        
        return view('stock-opnames.index', compact(
            'stockOpnames', 
            'products',
            'totalOpname',
            'totalSesuai',
            'totalSelisih',
            'totalPcsSelisih'
        ));
    }

    public function create()
    {
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        return view('stock-opnames.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_opname' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:produk,id',
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        $tanggal_opname = $request->tanggal_opname;

        try {
            DB::beginTransaction();

            $savedCount = 0;

            foreach ($request->items as $item) {
                if (!isset($item['stok_fisik']) || $item['stok_fisik'] === '') {
                    continue;
                }

                $product = Product::findOrFail($item['product_id']);
                $stok_sistem = $product->stok_saat_ini;
                $stok_fisik = (int)$item['stok_fisik'];
                $selisih = $stok_fisik - $stok_sistem;
                $keterangan = $item['keterangan'] ?? null;

                StockOpname::create([
                    'product_id' => $product->id,
                    'stok_sistem' => $stok_sistem,
                    'stok_fisik' => $stok_fisik,
                    'selisih' => $selisih,
                    'tanggal_opname' => $tanggal_opname,
                    'keterangan' => $keterangan,
                    'user_id' => auth()->id(),
                ]);

                if ($selisih != 0) {
                    // Update produk stok (handled below depending on selisih)
                    // Create penyesuaian
                    if ($selisih < 0) {
                        $outgoing = OutgoingGood::create([
                            'product_id' => $product->id,
                            'tanggal_keluar' => $tanggal_opname,
                            'jumlah' => abs($selisih),
                            'jenis_keluar' => 'penyesuaian',
                            'keterangan' => 'Penyesuaian stok opname: ' . $keterangan,
                            'user_id' => auth()->id(),
                        ]);
                        // deduct from FIFO
                        app(\App\Services\FifoService::class)->deductStock($product->id, abs($selisih), $outgoing->id);
                    } else {
                        $date = str_replace('-', '', $tanggal_opname);
                        $countToday = IncomingGood::whereDate('tanggal_masuk', $tanggal_opname)->count() + 1;
                        $batchCode = 'BM-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                        $incoming = IncomingGood::create([
                            'product_id' => $product->id,
                            'tanggal_masuk' => $tanggal_opname,
                            'jumlah' => $selisih,
                            'satuan' => $product->satuan,
                            'batch_code' => $batchCode,
                            'sumber_import' => 'Stok Opname',
                            'user_id' => auth()->id(),
                        ]);
                        
                        \App\Models\StockBatch::create([
                            'product_id' => $product->id,
                            'incoming_good_id' => $incoming->id,
                            'batch_code' => $batchCode,
                            'tanggal_masuk' => $tanggal_opname,
                            'jumlah_awal' => $selisih,
                            'jumlah_sisa' => $selisih,
                            'satuan' => $product->satuan,
                        ]);

                        $product->increment('stok_saat_ini', $selisih);
                    }
                }
                $savedCount++;
            }

            if ($savedCount === 0) {
                throw new \Exception('Tidak ada data stok opname yang diisi.');
            }

            DB::commit();

            LogActivity::log('opname', 'Stok Opname', "Menyimpan penyesuaian stok opname untuk {$savedCount} jenis produk.");

            return redirect()->route('stok-opname.index')->with('success', $savedCount . ' data stok opname berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan stok opname: ' . $e->getMessage()]);
        }
    }
}
