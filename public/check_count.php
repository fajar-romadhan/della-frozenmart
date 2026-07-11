<?php
/**
 * Laravel 11 Database Record Counter
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check security key from .env or fallback
$envFile = dirname(__DIR__) . '/.env';
$secureKey = 'DellaFrozenMart2026_SecureKey'; // fallback
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (preg_match('/^DEMO_RESET_KEY=(.*)$/m', $envContent, $matches)) {
        $secureKey = trim($matches[1], "\"' ");
    }
}

if (!isset($_GET['key']) || $_GET['key'] !== $secureKey) {
    http_response_code(403);
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>403 Akses Ditolak</title>
        <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap' rel='stylesheet'>
        <style>
            body { font-family: 'Outfit', sans-serif; background-color: #0f172a; color: #f8fafc; text-align: center; padding: 5rem; }
            h1 { color: #f87171; }
            .key-info { background: #1e293b; padding: 1rem; border-radius: 8px; max-width: 500px; margin: 2rem auto; font-family: monospace; border: 1px solid rgba(255,255,255,0.08); color: #94a3b8; }
        </style>
    </head>
    <body>
        <h1>403 Akses Ditolak</h1>
        <p>Anda memerlukan token keamanan untuk mengakses halaman ini.</p>
        <div class='key-info'>Hubungi administrator untuk token yang valid atau tambahkan parameter ?key=... pada URL.</div>
    </body>
    </html>";
    exit;
}

$baseDir = dirname(__DIR__);

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Laravel 11 - Record Counter</title>
    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f172a; color: #f8fafc; padding: 2rem; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); }
        h1 { font-size: 1.8rem; margin-bottom: 1.5rem; color: #38bdf8; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.5rem; text-align: center; }
        .count-item { display: flex; justify-content: space-between; padding: 0.8rem 1rem; background: #0f172a; border-radius: 8px; margin-bottom: 0.8rem; border: 1px solid rgba(255,255,255,0.05); }
        .count-label { font-weight: 600; color: #cbd5e1; }
        .count-value { font-weight: 700; color: #34d399; font-size: 1.1rem; }
        .btn { display: block; text-align: center; background: #0284c7; color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 1.5rem; border: none; }
        .btn:hover { background: #0369a1; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Jumlah Data di Database Hosting</h1>";

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Folder vendor tidak ditemukan.");
    }
    
    // Bootstrap Laravel
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $productCount = App\Models\Product::count();
    $categoryCount = App\Models\Category::count();
    $supplierCount = App\Models\Supplier::count();
    $incomingCount = App\Models\IncomingGood::count();
    $outgoingCount = App\Models\OutgoingGood::count();
    $saleCount = App\Models\Sale::count();
    $minSaleDate = App\Models\Sale::min('tanggal_penjualan') ?? 'Tidak ada';
    $maxSaleDate = App\Models\Sale::max('tanggal_penjualan') ?? 'Tidak ada';
    
    echo "
    <div class='count-item'>
        <span class='count-label'>Total Produk</span>
        <span class='count-value'>{$productCount} Item</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Total Kategori</span>
        <span class='count-value'>{$categoryCount} Kategori</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Total Supplier</span>
        <span class='count-value'>{$supplierCount} Supplier</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Transaksi Barang Masuk</span>
        <span class='count-value'>{$incomingCount} Record</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Transaksi Barang Keluar</span>
        <span class='count-value'>{$outgoingCount} Record</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Total Transaksi Penjualan (Excel)</span>
        <span class='count-value'>{$saleCount} Record</span>
    </div>
    <div class='count-item'>
        <span class='count-label'>Rentang Tanggal Penjualan</span>
        <span class='count-value'>{$minSaleDate} s/d {$maxSaleDate}</span>
    </div>
    ";
    
} catch (Exception $e) {
    echo "<div style='color: #f87171; background: rgba(239, 68, 68, 0.1); padding: 1rem; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2); font-family: monospace;'>" . $e->getMessage() . "</div>";
}

echo "
    <a href='/produk' class='btn'>Kembali ke Kelola Produk</a>
</div>
</body>
</html>";
