<?php
namespace App\Http\Controllers;

use App\Models\OutgoingGood;
use App\Models\OutgoingGoodDetail;
use App\Models\Product;
use App\Services\FifoService;
use App\Services\NotificationService;
use App\Services\SafetyStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogActivity;

class OutgoingGoodController extends Controller
{
    protected $fifoService;
    protected $notificationService;
    protected $safetyStockService;

    public function __construct(FifoService $fifoService, NotificationService $notificationService, SafetyStockService $safetyStockService)
    {
        $this->fifoService = $fifoService;
        $this->notificationService = $notificationService;
        $this->safetyStockService = $safetyStockService;
    }

    public function index(Request $request)
    {
        $query = OutgoingGood::with(['product', 'user'])->latest('tanggal_keluar');
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('jenis_keluar')) {
            $query->where('jenis_keluar', $request->jenis_keluar);
        }

        // Dynamic stats based on same filters
        $statsQuery = OutgoingGood::query();
        if ($request->filled('tanggal_dari')) {
            $statsQuery->whereDate('tanggal_keluar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $statsQuery->whereDate('tanggal_keluar', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('product_id')) {
            $statsQuery->where('product_id', $request->product_id);
        }
        if ($request->filled('jenis_keluar')) {
            $statsQuery->where('jenis_keluar', $request->jenis_keluar);
        }

        $totalTransaksi = $statsQuery->count();
        $totalProdukKeluar = $statsQuery->sum('jumlah');
        $totalPenjualan = (clone $statsQuery)->where('jenis_keluar', 'penjualan')->sum('jumlah');
        $totalExpired = (clone $statsQuery)->where('jenis_keluar', 'kedaluwarsa')->sum('jumlah');

        $outgoingGoods = $query->paginate(10)->withQueryString();
        $products = Product::orderBy('nama_produk')->get();
        $jenisKeluarOptions = ['penjualan', 'rusak', 'kedaluwarsa', 'penyesuaian'];

        return view('outgoing-goods.index', compact(
            'outgoingGoods', 
            'products', 
            'jenisKeluarOptions',
            'totalTransaksi',
            'totalProdukKeluar',
            'totalPenjualan',
            'totalExpired'
        ));
    }

    public function create()
    {
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        return view('outgoing-goods.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'tanggal_keluar' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'jenis_keluar' => 'required|in:penjualan,rusak,kedaluwarsa,penyesuaian',
            'keterangan' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->jumlah > $product->stok_saat_ini) {
            return back()->withInput()->withErrors(['jumlah' => 'Jumlah barang keluar (' . $request->jumlah . ') melebihi stok saat ini (' . $product->stok_saat_ini . ').']);
        }

        try {
            DB::beginTransaction();

            $outgoingGood = OutgoingGood::create([
                'product_id' => $product->id,
                'tanggal_keluar' => $request->tanggal_keluar,
                'jumlah' => $request->jumlah,
                'jenis_keluar' => $request->jenis_keluar,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            $this->fifoService->deductStock($product->id, $request->jumlah, $outgoingGood->id);
            
            // Check if status changed
            $analysis = $this->safetyStockService->calculate($product);
            if ($analysis['status_stok'] === 'Warning' || $analysis['status_stok'] === 'Order') {
                $this->notificationService->createStockWarning($product, $analysis['status_stok']);
            }

            DB::commit();

            LogActivity::log('outgoing', 'Tambah Barang Keluar', "Mengeluarkan produk '{$product->nama_produk}' ({$product->kode_produk}) sebanyak {$request->jumlah} pcs untuk jenis: {$request->jenis_keluar}.");

            return redirect()->route('barang-keluar.index')->with('success', 'Barang keluar berhasil disimpan dan stok telah dikurangi menggunakan metode FIFO.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memproses: ' . $e->getMessage()]);
        }
    }

    public function show(OutgoingGood $barang_keluar)
    {
        $barang_keluar->load(['product', 'user', 'outgoingGoodDetails.stockBatch']);
        return view('outgoing-goods.show', compact('barang_keluar'));
    }
}
