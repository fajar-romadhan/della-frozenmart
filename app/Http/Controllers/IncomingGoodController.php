<?php
namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\LogActivity;

class IncomingGoodController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomingGood::with(['product', 'supplier', 'user', 'stockBatch']);
        
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

        // Stats calculation (dynamic based on filters)
        $statsQuery = IncomingGood::query();
        if ($request->filled('tanggal_dari')) {
            $statsQuery->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $statsQuery->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $statsQuery->where('product_id', $request->product_id);
        }
        if ($request->filled('supplier_id')) {
            $statsQuery->where('supplier_id', $request->supplier_id);
        }

        $totalTransaksi = $statsQuery->count();
        $totalProdukDiterima = $statsQuery->sum('jumlah');
        $totalNilaiPembelian = $statsQuery->sum(\DB::raw('jumlah * harga_beli'));
        $totalLokasi = $statsQuery->distinct('id_lokasi')->count('id_lokasi');

        $incomingGoods = $query->orderBy('id')->paginate(10)->withQueryString();
        $products = Product::orderBy('nama_produk')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('incoming-goods.index', compact(
            'incomingGoods', 
            'products', 
            'suppliers', 
            'totalTransaksi', 
            'totalProdukDiterima', 
            'totalNilaiPembelian', 
            'totalLokasi'
        ));
    }

    public function create()
    {
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        
        return view('incoming-goods.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'id_lokasi' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:produk,id',
            'items.*.supplier_id' => 'required|exists:supplier,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
        ], [
            'tanggal_masuk.required' => 'Tanggal wajib diisi.',
            'id_lokasi.required' => 'ID Lokasi wajib diisi.',
            'items.required' => 'Minimal harus menambahkan 1 barang masuk.',
            'items.array' => 'Data barang tidak valid.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.supplier_id.required' => 'Supplier wajib dipilih.',
            'items.*.jumlah.required' => 'Jumlah wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'items.*.harga_beli.required' => 'Harga satuan wajib diisi.',
            'items.*.harga_beli.min' => 'Harga satuan minimal 0.',
        ]);

        \DB::transaction(function() use ($request) {
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Generate batch code BM-YYYYMMDD-XXXX
                $date = str_replace('-', '', $request->tanggal_masuk);
                $countToday = IncomingGood::whereDate('tanggal_masuk', $request->tanggal_masuk)->count() + 1;
                $batchCode = 'BM-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
                
                // Expiry date default: 6 months after tanggal_masuk
                $expiry = Carbon::parse($request->tanggal_masuk)->addMonths(6)->toDateString();

                $incomingGood = IncomingGood::create([
                    'product_id' => $product->id,
                    'supplier_id' => $item['supplier_id'],
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'jumlah' => $item['jumlah'],
                    'satuan' => $product->satuan,
                    'harga_beli' => $item['harga_beli'],
                    'tanggal_kedaluwarsa' => $expiry,
                    'batch_code' => $batchCode,
                    'sumber_import' => 'Manual',
                    'id_lokasi' => $request->id_lokasi,
                    'keterangan' => $item['keterangan'] ?? null,
                    'user_id' => auth()->id(),
                ]);

                StockBatch::create([
                    'product_id' => $product->id,
                    'incoming_good_id' => $incomingGood->id,
                    'batch_code' => $batchCode,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'tanggal_kedaluwarsa' => $expiry,
                    'jumlah_awal' => $item['jumlah'],
                    'jumlah_sisa' => $item['jumlah'],
                    'satuan' => $product->satuan,
                ]);

                $product->increment('stok_saat_ini', $item['jumlah']);
            }
        });

        $totalItems = count($request->items);
        $totalQty = collect($request->items)->sum('jumlah');
        LogActivity::log('incoming', 'Tambah Barang Masuk', "Mencatat transaksi barang masuk sebanyak {$totalItems} jenis barang dengan total {$totalQty} pcs di lokasi {$request->id_lokasi}.");

        if ($request->input('action') === 'save_and_create_new') {
            return redirect()->route('barang-masuk.create')->with('success', 'Barang masuk berhasil disimpan.');
        }
        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil disimpan.');
    }

    public function show(IncomingGood $barang_masuk)
    {
        $barang_masuk->load(['product', 'supplier', 'user', 'stockBatch']);
        return view('incoming-goods.show', compact('barang_masuk'));
    }
}
