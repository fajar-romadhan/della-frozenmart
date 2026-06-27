<?php
namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockBatch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'product', 'user'])->latest('tanggal_pemesanan');
        
        if ($request->filled('status_pemesanan')) {
            $query->where('status_pemesanan', $request->status_pemesanan);
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();
        
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        
        $selectedProductId = $request->product_id;
        
        return view('purchase-orders.create', compact('suppliers', 'products', 'selectedProductId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'product_id' => 'required|exists:produk,id',
            'jumlah_pesan' => 'required|integer|min:1',
            'keterangan' => 'nullable|string'
        ]);

        PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'product_id' => $request->product_id,
            'jumlah_pesan' => $request->jumlah_pesan,
            'tanggal_pemesanan' => date('Y-m-d'),
            'status_pemesanan' => 'draft',
            'keterangan' => $request->keterangan,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('pemesanan-supplier.index')->with('success', 'Pemesanan berhasil dibuat (Draft).');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'product', 'user']);
        return view('purchase-orders.show', ['pemesanan_supplier' => $purchaseOrder]);
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:dipesan,diterima,dibatalkan'
        ]);

        $purchaseOrder->update(['status_pemesanan' => $request->status]);

        return redirect()->back()->with('success', 'Status pemesanan berhasil diubah menjadi ' . ucfirst($request->status));
    }

    public function createIncomingFromOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status_pemesanan !== 'diterima') {
            return redirect()->back()->withErrors(['error' => 'Pemesanan belum berstatus diterima.']);
        }

        // Prevent double receive
        $exists = IncomingGood::where('sumber_import', 'Purchase Order #' . $purchaseOrder->id)->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['error' => 'Barang masuk untuk Pemesanan ini sudah pernah diproses.']);
        }

        DB::beginTransaction();
        try {
            $tanggal = date('Y-m-d');
            $dateStr = str_replace('-', '', $tanggal);
            $countToday = IncomingGood::whereDate('tanggal_masuk', $tanggal)->count() + 1;
            $batchCode = 'BM-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $incoming = IncomingGood::create([
                'product_id' => $purchaseOrder->product_id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'tanggal_masuk' => $tanggal,
                'jumlah' => $purchaseOrder->jumlah_pesan,
                'satuan' => $purchaseOrder->product->satuan,
                'batch_code' => $batchCode,
                'sumber_import' => 'Purchase Order #' . $purchaseOrder->id,
                'user_id' => auth()->id(),
            ]);

            StockBatch::create([
                'product_id' => $purchaseOrder->product_id,
                'incoming_good_id' => $incoming->id,
                'batch_code' => $batchCode,
                'tanggal_masuk' => $tanggal,
                'jumlah_awal' => $purchaseOrder->jumlah_pesan,
                'jumlah_sisa' => $purchaseOrder->jumlah_pesan,
                'satuan' => $purchaseOrder->product->satuan,
            ]);

            $purchaseOrder->product->increment('stok_saat_ini', $purchaseOrder->jumlah_pesan);
            
            // Mark as done? Or keep it as diterima? Just add to note
            $purchaseOrder->update([
                'keterangan' => $purchaseOrder->keterangan . "\nBarang Masuk telah dibuat otomatis pada " . $tanggal
            ]);

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dibuat dari pemesanan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal membuat barang masuk: ' . $e->getMessage()]);
        }
    }
}
