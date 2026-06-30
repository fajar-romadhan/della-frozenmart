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
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('produk', ProductController::class);
        Route::resource('kategori', CategoryController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('pengguna', UserController::class);
        Route::post('pengguna/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('pengguna.toggle-status');
        Route::post('pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->name('pengguna.reset-password');
    });
    
    // Admin + Manager routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::resource('barang-masuk', IncomingGoodController::class);
        Route::resource('barang-keluar', OutgoingGoodController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('stok-opname', StockOpnameController::class)->only(['index', 'create', 'store']);
        
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

    // Manager only routes
    Route::middleware(['role:manager'])->group(function () {
        // Import Penjualan
        Route::get('import-penjualan', [ImportPenjualanController::class, 'index'])->name('import-penjualan.index');
        Route::post('import-penjualan/preview', [ImportPenjualanController::class, 'preview'])->name('import-penjualan.preview');
        Route::post('import-penjualan/store', [ImportPenjualanController::class, 'store'])->name('import-penjualan.store');
    });

    // Owner only routes
    Route::middleware(['role:owner'])->group(function () {
        // Status Stok
        Route::get('status-stok', [InventoryAnalysisController::class, 'statusStok'])->name('status-stok');
    });

    // Admin + Owner routes
    Route::middleware(['role:admin,owner'])->group(function () {
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
        Route::get('export/barang-masuk/excel', [ReportController::class, 'exportBarangMasukExcel'])->name('export.barang-masuk.excel');
        
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
