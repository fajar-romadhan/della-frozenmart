<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$baseDir = dirname(__DIR__);
try {
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $report = [];

    // Query 1: Eager-loaded pagination
    $start = microtime(true);
    $query = App\Models\OutgoingGood::with(['product', 'user', 'outgoingGoodDetails.stockBatch'])->latest('tanggal_keluar');
    $outgoingGoods = $query->paginate(10);
    $report['pagination_time_ms'] = (microtime(true) - $start) * 1000;

    // Query 2: Stats
    $start = microtime(true);
    $statsQuery = App\Models\OutgoingGood::query();
    $totalTransaksi = $statsQuery->count();
    $totalProdukKeluar = $statsQuery->sum('jumlah');
    $totalPenjualan = (clone $statsQuery)->where('jenis_keluar', 'penjualan')->sum('jumlah');
    $totalExpired = (clone $statsQuery)->where('jenis_keluar', 'kedaluwarsa')->sum('jumlah');
    $report['stats_queries_time_ms'] = (microtime(true) - $start) * 1000;
    
    $report['total_transaksi'] = $totalTransaksi;
    $report['total_produk_keluar'] = $totalProdukKeluar;

    header('Content-Type: application/json');
    echo json_encode($report, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
