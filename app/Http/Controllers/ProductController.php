<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LogActivity;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search and filter.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'latestIncomingGood.supplier']);

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $products = $query->orderBy('nama_produk')->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('produk.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('produk.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:kategori,id'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'tanggal_kedaluwarsa' => ['nullable', 'date'],
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'satuan.required' => 'Satuan wajib diisi.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.integer' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
        ]);

        // Generate kode_produk: PRD-XXXX
        $validated['kode_produk'] = $this->generateKodeProduk();
        $validated['stok_saat_ini'] = 0;
        $validated['status_aktif'] = true;

        // Standardize satuan
        $validated['satuan'] = $this->standardizeSatuan($validated['satuan']);

        $product = Product::create($validated);
        LogActivity::log('create', 'Tambah Produk Baru', "Menambahkan produk baru '{$product->nama_produk}' ({$product->kode_produk}).");

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product with details.
     */
    public function show(Product $produk)
    {
        $produk->load('category');

        // Stock batches with remaining stock
        $stockBatches = StockBatch::where('product_id', $produk->id)
            ->orderBy('tanggal_masuk', 'desc')
            ->paginate(10, ['*'], 'batch_page');

        // Recent incoming goods
        $recentIncoming = IncomingGood::where('product_id', $produk->id)
            ->with('supplier')
            ->orderBy('tanggal_masuk', 'desc')
            ->limit(10)
            ->get();

        // Recent outgoing goods
        $recentOutgoing = OutgoingGood::where('product_id', $produk->id)
            ->with('user')
            ->orderBy('tanggal_keluar', 'desc')
            ->limit(10)
            ->get();

        return view('produk.show', compact('produk', 'stockBatches', 'recentIncoming', 'recentOutgoing'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $produk)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('produk.edit', compact('produk', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $produk)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:kategori,id'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'tanggal_kedaluwarsa' => ['nullable', 'date'],
            'status_aktif' => ['sometimes', 'boolean'],
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'satuan.required' => 'Satuan wajib diisi.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.integer' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
        ]);

        // Standardize satuan
        $validated['satuan'] = $this->standardizeSatuan($validated['satuan']);

        $produk->update($validated);
        LogActivity::log('update', 'Perbarui Data Produk', "Memperbarui detail data produk '{$produk->nama_produk}' ({$produk->kode_produk}).");

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product (with transaction check).
     */
    public function destroy(Product $produk)
    {
        // Check if product has transactions
        $hasIncoming = IncomingGood::where('product_id', $produk->id)->exists();
        $hasOutgoing = OutgoingGood::where('product_id', $produk->id)->exists();

        if ($hasIncoming || $hasOutgoing) {
            return redirect()->route('produk.index')
                ->with('warning', "Produk '{$produk->nama_produk}' tidak dapat dihapus karena memiliki riwayat transaksi. Anda dapat menonaktifkan produk ini sebagai gantinya.");
        }

        $produkName = $produk->nama_produk;
        $produkKode = $produk->kode_produk;
        $produk->delete();
        LogActivity::log('delete', 'Hapus Produk', "Menghapus data produk '{$produkName}' ({$produkKode}) dari sistem.");

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Generate sequential product code: PRD-XXXX.
     */
    private function generateKodeProduk(): string
    {
        $lastProduct = Product::orderByRaw("CAST(SUBSTRING(kode_produk, 5) AS UNSIGNED) DESC")->first();

        if ($lastProduct && preg_match('/PRD-(\d+)/', $lastProduct->kode_produk, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'PRD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Standardize unit names.
     */
    private function standardizeSatuan(string $satuan): string
    {
        $mapping = [
            'pcs' => 'PCS', 'Pcs' => 'PCS', 'PCS' => 'PCS', 'pc' => 'PCS',
            'pack' => 'PACK', 'Pack' => 'PACK', 'PACK' => 'PACK',
            'box' => 'BOX', 'Box' => 'BOX', 'BOX' => 'BOX',
            'kg' => 'KG', 'Kg' => 'KG', 'KG' => 'KG',
            'gram' => 'GRAM', 'Gram' => 'GRAM', 'GRAM' => 'GRAM', 'gr' => 'GRAM',
            'liter' => 'LITER', 'Liter' => 'LITER', 'LITER' => 'LITER', 'ltr' => 'LITER',
            'lusin' => 'LUSIN', 'Lusin' => 'LUSIN', 'LUSIN' => 'LUSIN',
            'botol' => 'BOTOL', 'Botol' => 'BOTOL', 'BOTOL' => 'BOTOL',
            'bungkus' => 'BUNGKUS', 'Bungkus' => 'BUNGKUS', 'BUNGKUS' => 'BUNGKUS',
            'karton' => 'KARTON', 'Karton' => 'KARTON', 'KARTON' => 'KARTON',
        ];

        return $mapping[trim($satuan)] ?? strtoupper(trim($satuan));
    }
}
