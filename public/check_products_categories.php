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

    $products = App\Models\Product::with('category')->get()->map(function($p) {
        return [
            'id' => $p->id,
            'nama_produk' => $p->nama_produk,
            'category_id' => $p->category_id,
            'category_name' => $p->category ? $p->category->nama_kategori : null,
        ];
    });

    header('Content-Type: application/json');
    echo json_encode($products, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
