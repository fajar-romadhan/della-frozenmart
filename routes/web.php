<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportFakturController;
use App\Http\Controllers\ImportPenjualanController;
use App\Http\Controllers\IncomingGoodController;
use App\Http\Controllers\InventoryAnalysisController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OutgoingGoodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/', fn() => redirect('/login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// All authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales-data', [DashboardController::class, 'getSalesData'])->name('dashboard.sales-data');
    
    // Ganti Password
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('password.update');
    
    // Global Search API Route
    Route::get('/global-search', function (Illuminate\Http\Request $request) {
        $query = $request->query('q');
        if (strlen($query) < 2) {
            return response()->json(['menus' => [], 'products' => []]);
        }
        
        $user = auth()->user();
        $role = $user ? $user->role : 'admin';
        
        // Define menus to search
        $allMenus = [];
        $allMenus[] = ['name' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'ph-house'];
        
        if ($role === 'admin' || $role === 'owner') {
            $allMenus[] = ['name' => 'Kelola Data Produk', 'url' => route('produk.index'), 'icon' => 'ph-package'];
            $allMenus[] = ['name' => 'Kelola Supplier', 'url' => route('supplier.index'), 'icon' => 'ph-truck'];
            $allMenus[] = ['name' => 'Kelola Pengguna', 'url' => route('pengguna.index'), 'icon' => 'ph-users'];
        }
        
        if ($role === 'admin' || $role === 'manager' || $role === 'owner') {
            $allMenus[] = ['name' => 'Barang Masuk', 'url' => route('barang-masuk.index'), 'icon' => 'ph-download-simple'];
            $allMenus[] = ['name' => 'Barang Keluar', 'url' => route('barang-keluar.index'), 'icon' => 'ph-upload-simple'];
            $allMenus[] = ['name' => 'Stok Opname', 'url' => route('stok-opname.index'), 'icon' => 'ph-scales'];
            $allMenus[] = ['name' => 'Import Faktur Pembelian', 'url' => route('import-faktur.index'), 'icon' => 'ph-file-arrow-down'];
            $allMenus[] = ['name' => 'Analisis Persediaan (Safety Stock)', 'url' => route('analisis.index'), 'icon' => 'ph-archive'];
        }
        
        if ($role === 'manager' || $role === 'owner') {
            $allMenus[] = ['name' => 'Import Penjualan', 'url' => route('import-penjualan.index'), 'icon' => 'ph-file-arrow-up'];
        }
        
        $allMenus[] = ['name' => 'Laporan Penjualan', 'url' => route('laporan.penjualan'), 'icon' => 'ph-chart-line'];
        $allMenus[] = ['name' => 'Laporan Persediaan', 'url' => route('laporan.persediaan'), 'icon' => 'ph-archive-box'];
        $allMenus[] = ['name' => 'Laporan Barang Masuk', 'url' => route('laporan.barang-masuk'), 'icon' => 'ph-file-arrow-down'];
        $allMenus[] = ['name' => 'Laporan Barang Keluar', 'url' => route('laporan.barang-keluar'), 'icon' => 'ph-file-arrow-up'];
        $allMenus[] = ['name' => 'Laporan Pemesanan Produk', 'url' => route('pemesanan-supplier.index'), 'icon' => 'ph-receipt'];
        $allMenus[] = ['name' => 'Notifikasi Sistem', 'url' => route('notifikasi.index'), 'icon' => 'ph-bell'];
        
        $filteredMenus = [];
        foreach ($allMenus as $menu) {
            if (stripos($menu['name'], $query) !== false) {
                $filteredMenus[] = $menu;
            }
        }
        
        // Search products
        $products = \App\Models\Product::where('nama_produk', 'LIKE', "%{$query}%")
            ->orWhere('kode_produk', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'nama_produk', 'kode_produk']);
            
        $role = auth()->user()->role ?? 'admin';
        $filteredProducts = [];
        foreach ($products as $p) {
            $url = '#';
            if ($role === 'admin') {
                $url = route('produk.index') . '?search=' . urlencode($p->kode_produk);
            } elseif ($role === 'manager') {
                // Manager has access to Inventory Analysis Show page
                $url = route('analisis.show', $p->id);
            } else {
                // Owner and others go to Inventory Report
                $url = route('laporan.persediaan');
            }

            $filteredProducts[] = [
                'name' => $p->nama_produk . ' (' . $p->kode_produk . ')',
                'url' => $url,
                'icon' => 'ph-package'
            ];
        }
        
        return response()->json([
            'menus' => $filteredMenus,
            'products' => $filteredProducts
        ]);
    })->name('global.search');
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('produk', ProductController::class);
        Route::resource('kategori', CategoryController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('pengguna', UserController::class);
        Route::post('pengguna/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('pengguna.toggle-status');
        Route::post('pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->name('pengguna.reset-password');
        Route::resource('stok-opname', StockOpnameController::class)->only(['index', 'create', 'store']);
    });
    
    // Admin + Manager routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::resource('barang-masuk', IncomingGoodController::class);
        Route::resource('barang-keluar', OutgoingGoodController::class)->only(['index', 'create', 'store', 'show']);
        
        // Import Faktur Pembelian
        Route::get('import-faktur-pembelian', [ImportFakturController::class, 'index'])->name('import-faktur.index');
        Route::post('import-faktur-pembelian/preview', [ImportFakturController::class, 'preview'])->name('import-faktur.preview');
        Route::post('import-faktur-pembelian/store', [ImportFakturController::class, 'store'])->name('import-faktur.store');
        
        // Analisis Persediaan
        Route::get('analisis-persediaan', [InventoryAnalysisController::class, 'index'])->name('analisis.index');
        Route::post('analisis-persediaan/analyze/{product}', [InventoryAnalysisController::class, 'analyze'])->name('analisis.analyze');
        Route::post('analisis-persediaan/analyze-all', [InventoryAnalysisController::class, 'analyzeAll'])->name('analisis.analyze-all');
        Route::post('analisis-persediaan/upload-sales', [InventoryAnalysisController::class, 'uploadSales'])->name('analisis.upload-sales');
        Route::get('analisis-persediaan/{product}', [InventoryAnalysisController::class, 'show'])->name('analisis.show');
        
        // Pemesanan Supplier (write actions only)
        Route::resource('pemesanan-supplier', PurchaseOrderController::class)->except(['index', 'show'])->parameters([
            'pemesanan-supplier' => 'purchaseOrder'
        ]);
        Route::post('pemesanan-supplier/{purchaseOrder}/update-status', [PurchaseOrderController::class, 'updateStatus'])->name('pemesanan.update-status');
        Route::post('pemesanan-supplier/{purchaseOrder}/create-incoming', [PurchaseOrderController::class, 'createIncomingFromOrder'])->name('pemesanan.create-incoming');
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Import Penjualan
        Route::get('import-penjualan', [ImportPenjualanController::class, 'index'])->name('import-penjualan.index');
        Route::post('import-penjualan/preview', [ImportPenjualanController::class, 'preview'])->name('import-penjualan.preview');
        Route::post('import-penjualan/store', [ImportPenjualanController::class, 'store'])->name('import-penjualan.store');
    });

    // Manager only routes
    Route::middleware(['role:manager'])->group(function () {
        // Peramalan
        Route::get('peramalan', [App\Http\Controllers\ForecastingController::class, 'index'])->name('peramalan.index');
        Route::post('peramalan/calculate', [App\Http\Controllers\ForecastingController::class, 'calculate'])->name('peramalan.calculate');
    });

    // Admin + Owner + Manager routes
    Route::middleware(['role:admin,owner,manager'])->group(function () {
        // Status Stok
        Route::get('status-stok', [InventoryAnalysisController::class, 'statusStok'])->name('status-stok');

        // Laporan Barang Keluar
        Route::get('laporan/barang-keluar', [ReportController::class, 'barangKeluar'])->name('laporan.barang-keluar');
        
        // Export Barang Keluar
        Route::get('export/barang-keluar/pdf', [ReportController::class, 'exportBarangKeluarPdf'])->name('export.barang-keluar.pdf');
        // Route::get('export/barang-keluar/excel', [ReportController::class, 'exportBarangKeluarExcel'])->name('export.barang-keluar.excel');
    });
    
    // All roles routes
    Route::middleware(['role:admin,manager,owner'])->group(function () {
        // Laporan
        Route::get('laporan/penjualan', [ReportController::class, 'penjualan'])->name('laporan.penjualan');
        Route::get('laporan/persediaan', [ReportController::class, 'persediaan'])->name('laporan.persediaan');
        Route::get('laporan/barang-masuk', [ReportController::class, 'barangMasuk'])->name('laporan.barang-masuk');
        Route::get('laporan/import-log', [ReportController::class, 'importLog'])->name('laporan.import-log');
        
        // Export
        Route::get('export/penjualan/pdf', [ReportController::class, 'exportPenjualanPdf'])->name('export.penjualan.pdf');
        Route::get('export/penjualan/excel', [ReportController::class, 'exportPenjualanExcel'])->name('export.penjualan.excel');
        Route::get('export/persediaan/pdf', [ReportController::class, 'exportPersediaanPdf'])->name('export.persediaan.pdf');
        Route::get('export/persediaan/excel', [ReportController::class, 'exportPersediaanExcel'])->name('export.persediaan.excel');
        Route::get('export/barang-masuk/pdf', [ReportController::class, 'exportBarangMasukPdf'])->name('export.barang-masuk.pdf');
        // Route::get('export/barang-masuk/excel', [ReportController::class, 'exportBarangMasukExcel'])->name('export.barang-masuk.excel');
        
        // Notifikasi
        Route::get('notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
        Route::post('notifikasi/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
        Route::post('notifikasi/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.read-all');
        Route::get('notifikasi/count', [NotificationController::class, 'count'])->name('notifikasi.count');
        Route::get('notifikasi/latest-dropdown', [NotificationController::class, 'latestDropdown'])->name('notifikasi.latest-dropdown');
        
        // Pemesanan Supplier (read actions for all)
        Route::resource('pemesanan-supplier', PurchaseOrderController::class)->only(['index', 'show'])->parameters([
            'pemesanan-supplier' => 'purchaseOrder'
        ]);
    });
});
