<?php
/**
 * Laravel 11 Cache Clearer & Database Migrator
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
    <title>Laravel 11 - Cache & Migrate Tool</title>
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
    <h1>Laravel 11 - Cache & Migrate Tool</h1>";

// 1. Clear Bootstrap Cache
echo "<h3>1. Pembersihan File Cache bootstrap/cache/</h3>";
echo "<div class='log-box'>";
$cacheFiles = [
    $baseDir . '/bootstrap/cache/config.php',
    $baseDir . '/bootstrap/cache/routes-v7.php',
    $baseDir . '/bootstrap/cache/services.php',
    $baseDir . '/bootstrap/cache/packages.php',
];
$clearedAny = false;
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<span class='success'>[BERHASIL]</span> Menghapus file cache: " . basename($file) . "\n";
            $clearedAny = true;
        } else {
            echo "<span class='error'>[GAGAL]</span> Menghapus file cache: " . basename($file) . " (Periksa permission file)\n";
        }
    } else {
        echo "[INFO] File cache tidak ditemukan: " . basename($file) . " (Sudah bersih)\n";
    }
}
if (!$clearedAny) {
    echo "\nSemua cache bootstrap aman dan bersih!\n";
}
echo "</div>";

// 2. Run Artisan Migrate & Seed
echo "<h3>2. Menjalankan Migrasi & Database Seeder</h3>";
echo "<div class='log-box'>";
try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Folder vendor tidak ditemukan. Tidak dapat memuat Laravel.");
    }
    
    // Bootstrap Laravel
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    
    // Resolve Kernel console
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Check database connection and listing tables
    echo "Menghubungkan ke database...\n";
    $db = Illuminate\Support\Facades\DB::connection();
    $dbName = $db->getDatabaseName();
    echo "Database Terhubung: " . $dbName . "\n";
    
    // Get table list using raw query
    $tables = [];
    $rawTables = Illuminate\Support\Facades\DB::select("SHOW TABLES");
    foreach ($rawTables as $tableObj) {
        foreach ($tableObj as $key => $val) {
            $tables[] = $val;
        }
    }
    
    echo "Tabel saat ini: " . (empty($tables) ? "(Kosong)" : implode(', ', $tables)) . "\n\n";
    
    if (empty($tables)) {
        echo "Database kosong! Mulai menjalankan migrasi struktur tabel...\n";
        $exitCode = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        echo "Migrasi Struktur Tabel: " . ($exitCode === 0 ? "<span class='success'>SELESAI (OK)</span>" : "<span class='error'>GAGAL (Code: $exitCode)</span>") . "\n";
        
        echo "Menjalankan database seeder untuk data awal...\n";
        $seedExitCode = Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        echo "Seeder Data Awal: " . ($seedExitCode === 0 ? "<span class='success'>SELESAI (OK)</span>" : "<span class='error'>GAGAL (Code: $seedExitCode)</span>") . "\n";
    } else {
        echo "Database sudah berisi tabel. Menjalankan migrasi reguler (jika ada perubahan baru)...\n";
        $exitCode = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        echo "Hasil Migrasi: " . ($exitCode === 0 ? "<span class='success'>SELESAI/TIDAK ADA PERUBAHAN (OK)</span>" : "<span class='error'>GAGAL (Code: $exitCode)</span>") . "\n";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>[ERROR]</span> " . $e->getMessage() . "\n";
}
echo "</div>";

echo "<div style='text-align: center; margin-top: 2rem;'>
    <a href='/' class='btn'>Kembali ke Halaman Utama</a>
</div>
</div>
</body>
</html>";
