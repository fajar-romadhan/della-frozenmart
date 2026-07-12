<?php

namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Services\LogActivity;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers with incoming goods count.
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('incomingGoods');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Stats calculation
        $totalSupplier = Supplier::count();
        $supplierAktif = Supplier::where('status_aktif', true)->count();
        $supplierNonaktif = Supplier::where('status_aktif', false)->count();

        // Paginate with 7 rows to match mockup visual layout
        $suppliers = $query->orderBy('id')->paginate(7)->withQueryString();

        return view('supplier.index', compact('suppliers', 'totalSupplier', 'supplierAktif', 'supplierNonaktif'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'status_aktif' => ['nullable', 'boolean'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif') ? (bool)$request->status_aktif : true;

        $supplier = Supplier::create($validated);
        LogActivity::log('create', 'Tambah Supplier Baru', "Menambahkan supplier baru '{$supplier->nama_supplier}'.");

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        $supplier->loadCount('incomingGoods', 'purchaseOrders');

        $recentIncoming = IncomingGood::where('supplier_id', $supplier->id)
            ->with('product')
            ->orderBy('tanggal_masuk', 'desc')
            ->limit(10)
            ->get();

        $recentOrders = PurchaseOrder::where('supplier_id', $supplier->id)
            ->with('product')
            ->orderBy('tanggal_pemesanan', 'desc')
            ->limit(10)
            ->get();

        return view('supplier.show', compact('supplier', 'recentIncoming', 'recentOrders'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'status_aktif' => ['required', 'boolean'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'status_aktif.required' => 'Status aktif wajib dipilih.',
        ]);

        $supplier->update($validated);
        LogActivity::log('update', 'Perbarui Data Supplier', "Memperbarui detail data supplier '{$supplier->nama_supplier}'.");

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        $hasIncoming = $supplier->incomingGoods()->exists();
        $hasOrders = $supplier->purchaseOrders()->exists();

        if ($hasIncoming || $hasOrders) {
            return redirect()->route('supplier.index')
                ->with('error', "Supplier '{$supplier->nama_supplier}' tidak dapat dihapus karena memiliki riwayat transaksi barang masuk atau pemesanan.");
        }

        $supplierName = $supplier->nama_supplier;
        $supplier->delete();
        LogActivity::log('delete', 'Hapus Supplier', "Menghapus data supplier '{$supplierName}' dari sistem.");

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
