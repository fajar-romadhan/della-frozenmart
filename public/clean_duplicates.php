<?php
/**
 * Laravel 11 Database Duplicate Sales Cleaner
 * Created by Antigravity AI
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
    <title>Laravel 11 - Duplicate Sales Cleaner</title>
    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f172a; color: #f8fafc; padding: 2rem; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); }
        h1 { font-size: 1.8rem; margin-bottom: 1.5rem; color: #38bdf8; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.5rem; }
        h3 { font-size: 1.2rem; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #34d399; }
        .log-box { background: #0f172a; padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 1rem; max-height: 300px; overflow-y: auto; white-space: pre-wrap; }
        .btn { display: inline-block; background: #0284c7; color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 1rem; border: none; cursor: pointer; }
        .btn:hover { background: #0369a1; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Laravel 11 - Duplicate Sales Cleaner</h1>";

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Folder vendor tidak ditemukan. Tidak dapat memuat Laravel.");
    }
    
    // Bootstrap Laravel
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h3>Proses Pembersihan Data Penjualan Ganda (Duplicate Sales)</h3>";
    echo "<div class='log-box'>";

    $beforeCount = \Illuminate\Support\Facades\DB::table('penjualan')->count();
    
    // Delete duplicate sales rows (keeping the row with the lowest id)
    \Illuminate\Support\Facades\DB::statement("
        DELETE p1 FROM penjualan p1
        INNER JOIN penjualan p2 
        ON p1.product_id = p2.product_id 
        AND p1.tanggal_penjualan = p2.tanggal_penjualan 
        AND p1.jumlah_terjual = p2.jumlah_terjual 
        AND p1.id > p2.id
    ");

    $afterCount = \Illuminate\Support\Facades\DB::table('penjualan')->count();
    $deletedCount = $beforeCount - $afterCount;

    echo "- Jumlah baris sebelum dibersihkan: {$beforeCount} baris\n";
    echo "- Jumlah baris setelah dibersihkan: {$afterCount} baris\n";
    echo "- Total baris ganda yang dihapus: <span class='success'>{$deletedCount} baris</span>\n\n";

    // Recalculate Safety Stock & ROP for all active products to align numbers
    $safetyStockService = $app->make(\App\Services\SafetyStockService::class);
    $products = \App\Models\Product::where('status_aktif', true)->get();
    foreach ($products as $product) {
        $safetyStockService->calculate($product);
    }
    echo "Kalkulasi Ulang Analisis Persediaan: <span class='success'>SELESAI</span>\n";

    echo "</div>";
    echo "<h3 class='success'>Database Berhasil Dibersihkan dari Data Ganda!</h3>";

} catch (Exception $e) {
    echo "</div>";
    echo "<h3><span class='error'>[ERROR]</span> " . $e->getMessage() . "</h3>";
}

echo "<div style='text-align: center; margin-top: 2rem;'>
    <a href='/' class='btn'>Kembali ke Halaman Utama</a>
</div>
</div>
</body>
</html>";
