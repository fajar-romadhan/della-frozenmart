<?php
/**
 * Laravel 11 Database Incoming Goods Stock Mismatch Fixer
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
    <title>Laravel 11 - Stock Fixer Tool</title>
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
    <h1>Laravel 11 - Stock Fixer Tool</h1>";

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Folder vendor tidak ditemukan. Tidak dapat memuat Laravel.");
    }
    
    // Bootstrap Laravel
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h3>Proses Perbaikan Data Barang Masuk (Okey Sosis & Meru Lapis)</h3>";
    echo "<div class='log-box'>";

    Illuminate\Support\Facades\DB::transaction(function() {
        // 1. Perbaikan Okey Sosis 500GR (PRD-0011)
        $sosis = \App\Models\Product::where('kode_produk', 'PRD-0011')->first();
        if ($sosis) {
            $totalOutgoing = \App\Models\OutgoingGood::where('product_id', $sosis->id)->sum('jumlah');
            $totalIncoming = \App\Models\IncomingGood::where('product_id', $sosis->id)->sum('jumlah');
            $diff = $totalIncoming - $totalOutgoing; // 2670 - 2490 = 180

            echo "Produk: <strong>{$sosis->nama_produk}</strong>\n";
            echo "- Total Barang Masuk Saat Ini: {$totalIncoming} Pcs\n";
            echo "- Total Barang Keluar Saat Ini: {$totalOutgoing} Pcs\n";

            if ($diff > 0) {
                echo "- Selisih yang harus dikurangi: <span class='error'>{$diff} Pcs</span>\n";
                
                // Reduce from the latest incoming goods
                $incomingRecords = \App\Models\IncomingGood::where('product_id', $sosis->id)
                    ->orderBy('tanggal_masuk', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
                
                $toReduce = $diff;
                foreach ($incomingRecords as $record) {
                    if ($toReduce <= 0) break;
                    
                    if ($record->jumlah >= $toReduce) {
                        $record->jumlah -= $toReduce;
                        $record->save();
                        
                        // Update stock batch as well
                        $batch = \App\Models\StockBatch::where('incoming_good_id', $record->id)->first();
                        if ($batch) {
                            $batch->jumlah_awal -= $toReduce;
                            $batch->jumlah_sisa = max(0, $batch->jumlah_sisa - $toReduce);
                            $batch->save();
                        }
                        
                        echo "  [OK] Mengurangi {$toReduce} Pcs dari transaksi BM tanggal {$record->tanggal_masuk} (Batch: {$record->batch_code})\n";
                        $toReduce = 0;
                    } else {
                        $toReduce -= $record->jumlah;
                        $record->jumlah = 0;
                        $record->save();
                        
                        $batch = \App\Models\StockBatch::where('incoming_good_id', $record->id)->first();
                        if ($batch) {
                            $batch->jumlah_awal = 0;
                            $batch->jumlah_sisa = 0;
                            $batch->save();
                        }
                        
                        echo "  [OK] Mengosongkan transaksi BM tanggal {$record->tanggal_masuk} (Batch: {$record->batch_code})\n";
                    }
                }
                
                // Reset product current stock
                $sosis->stok_saat_ini = 0;
                $sosis->save();
                echo "- Stok Saat Ini berhasil disesuaikan ke: <span class='success'>0 Pcs</span>\n\n";
            } else {
                echo "- <span class='success'>Sudah sinkron / tidak perlu perbaikan.</span>\n\n";
            }
        }

        // 2. Perbaikan Meru Lapis Bogor (PRD-0022)
        $meru = \App\Models\Product::where('kode_produk', 'PRD-0022')->first();
        if ($meru) {
            $totalOutgoing = \App\Models\OutgoingGood::where('product_id', $meru->id)->sum('jumlah');
            $totalIncoming = \App\Models\IncomingGood::where('product_id', $meru->id)->sum('jumlah');
            $diff = $totalIncoming - $totalOutgoing; // 1570 - 1340 = 230

            echo "Produk: <strong>{$meru->nama_produk}</strong>\n";
            echo "- Total Barang Masuk Saat Ini: {$totalIncoming} Pcs\n";
            echo "- Total Barang Keluar Saat Ini: {$totalOutgoing} Pcs\n";

            if ($diff > 0) {
                echo "- Selisih yang harus dikurangi: <span class='error'>{$diff} Pcs</span>\n";
                
                // Reduce from the latest incoming goods
                $incomingRecords = \App\Models\IncomingGood::where('product_id', $meru->id)
                    ->orderBy('tanggal_masuk', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
                
                $toReduce = $diff;
                foreach ($incomingRecords as $record) {
                    if ($toReduce <= 0) break;
                    
                    if ($record->jumlah >= $toReduce) {
                        $record->jumlah -= $toReduce;
                        $record->save();
                        
                        // Update stock batch as well
                        $batch = \App\Models\StockBatch::where('incoming_good_id', $record->id)->first();
                        if ($batch) {
                            $batch->jumlah_awal -= $toReduce;
                            $batch->jumlah_sisa = max(0, $batch->jumlah_sisa - $toReduce);
                            $batch->save();
                        }
                        
                        echo "  [OK] Mengurangi {$toReduce} Pcs dari transaksi BM tanggal {$record->tanggal_masuk} (Batch: {$record->batch_code})\n";
                        $toReduce = 0;
                    } else {
                        $toReduce -= $record->jumlah;
                        $record->jumlah = 0;
                        $record->save();
                        
                        $batch = \App\Models\StockBatch::where('incoming_good_id', $record->id)->first();
                        if ($batch) {
                            $batch->jumlah_awal = 0;
                            $batch->jumlah_sisa = 0;
                            $batch->save();
                        }
                        
                        echo "  [OK] Mengosongkan transaksi BM tanggal {$record->tanggal_masuk} (Batch: {$record->batch_code})\n";
                    }
                }
                
                // Reset product current stock
                $meru->stok_saat_ini = 0;
                $meru->save();
                echo "- Stok Saat Ini berhasil disesuaikan ke: <span class='success'>0 Pcs</span>\n\n";
            } else {
                echo "- <span class='success'>Sudah sinkron / tidak perlu perbaikan.</span>\n\n";
            }
        }
    });

    // Recalculate Safety Stock for all products to reflect updated numbers
    $safetyStockService = $app->make(\App\Services\SafetyStockService::class);
    $products = \App\Models\Product::where('status_aktif', true)->get();
    foreach ($products as $product) {
        $safetyStockService->calculate($product);
    }
    echo "Kalkulasi Ulang Analisis Persediaan: <span class='success'>SELESAI</span>\n";

    echo "</div>";
    echo "<h3 class='success'>Perbaikan Data Berhasil Diselesaikan!</h3>";

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
