<?php
/**
 * Analisis Persediaan Diagnostic Tool
 * Memeriksa sumber data penjualan dan status analisis per produk.
 * Berguna untuk mendiagnosa jika AU/MU/SS/ROP menunjukkan 0.
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

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Folder vendor tidak ditemukan.");
    }

    // Bootstrap Laravel
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();

    // Get all active products
    $products = App\Models\Product::where('status_aktif', true)->orderBy('kode_produk')->get();

    $diagnosticData = [];
    $warnings = [];

    foreach ($products as $product) {
        // Count records in penjualan table
        $saleCount = App\Models\Sale::where('product_id', $product->id)->count();
        $saleMin = App\Models\Sale::where('product_id', $product->id)->min('tanggal_penjualan');
        $saleMax = App\Models\Sale::where('product_id', $product->id)->max('tanggal_penjualan');
        $saleTotal = App\Models\Sale::where('product_id', $product->id)->sum('jumlah_terjual');

        // Count records in barang_keluar table (jenis penjualan)
        $outCount = App\Models\OutgoingGood::where('product_id', $product->id)
            ->where('jenis_keluar', 'penjualan')->count();
        $outMin = App\Models\OutgoingGood::where('product_id', $product->id)
            ->where('jenis_keluar', 'penjualan')->min('tanggal_keluar');
        $outMax = App\Models\OutgoingGood::where('product_id', $product->id)
            ->where('jenis_keluar', 'penjualan')->max('tanggal_keluar');
        $outTotal = App\Models\OutgoingGood::where('product_id', $product->id)
            ->where('jenis_keluar', 'penjualan')->sum('jumlah');

        // Get latest analysis
        $analysis = App\Models\InventoryAnalysis::where('product_id', $product->id)->latest()->first();

        $hasData = ($saleCount > 0 || $outCount > 0);
        $auIsZero = $analysis && $analysis->average_usage == 0;

        if ($hasData && $auIsZero) {
            $warnings[] = $product->kode_produk . ' (' . $product->nama_produk . ')';
        }

        $diagnosticData[] = [
            'product' => $product,
            'sale_count' => $saleCount,
            'sale_range' => $saleMin ? ($saleMin . ' s/d ' . $saleMax) : '-',
            'sale_total' => $saleTotal,
            'out_count' => $outCount,
            'out_range' => $outMin ? ($outMin . ' s/d ' . $outMax) : '-',
            'out_total' => $outTotal,
            'analysis' => $analysis,
            'has_data' => $hasData,
            'au_is_zero' => $auIsZero,
        ];
    }

} catch (Exception $e) {
    echo "<div style='color: #f87171; padding: 2rem; font-family: monospace;'>Error: " . $e->getMessage() . "</div>";
    exit;
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostik Analisis Persediaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 45%);
        }
        .container {
            width: 100%; max-width: 1200px; margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        header {
            text-align: center; margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }
        header h1 {
            font-size: 1.8rem; font-weight: 700;
            background: linear-gradient(135deg, #60a5fa, #34d399);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 0.3rem;
        }
        header p { color: var(--text-muted); }
        .warning-box {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .ok-box {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        table {
            width: 100%; border-collapse: collapse;
            font-size: 0.85rem;
        }
        th {
            background: rgba(0,0,0,0.3);
            padding: 0.7rem 0.5rem;
            text-align: center;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }
        td {
            padding: 0.6rem 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            text-align: center;
            color: #cbd5e1;
        }
        tr:hover { background: rgba(255,255,255,0.03); }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .badge-ok { background: rgba(16,185,129,0.2); color: #34d399; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
        .badge-warn { background: rgba(239,68,68,0.2); color: #f87171; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
        .badge-empty { background: rgba(148,163,184,0.2); color: #94a3b8; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
        .highlight-row { background: rgba(239, 68, 68, 0.06) !important; }
        .btn {
            display: inline-block; text-align: center;
            background: #0284c7; color: white;
            padding: 0.6rem 1.2rem; border-radius: 6px;
            text-decoration: none; font-weight: 600;
            margin-top: 1.5rem; border: none;
        }
        .btn:hover { background: #0369a1; }
        footer {
            margin-top: 2rem; text-align: center;
            color: var(--text-muted); font-size: 0.85rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Diagnostik Analisis Persediaan</h1>
        <p>Memeriksa sumber data penjualan dan status analisis per produk</p>
    </header>

    <?php if (!empty($warnings)): ?>
        <div class="warning-box">
            ⚠️ TERDETEKSI <?php echo count($warnings); ?> PRODUK dengan data penjualan tapi AU = 0:
            <br><small><?php echo implode(', ', $warnings); ?></small>
            <br><br>Penyebab: Data penjualan ada tapi SafetyStockService mungkin belum membaca sumber yang tepat. Klik "Analisa Ulang Semua Produk" di halaman Analisis Persediaan.
        </div>
    <?php else: ?>
        <div class="ok-box">
            ✅ Tidak ada anomali terdeteksi. Semua produk yang punya data penjualan sudah memiliki AU > 0.
        </div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th class="text-left">Nama Produk</th>
                    <th colspan="3" style="background: rgba(59,130,246,0.15); color: #60a5fa;">Tabel Penjualan (Sale)</th>
                    <th colspan="3" style="background: rgba(16,185,129,0.15); color: #34d399;">Tabel Barang Keluar (penjualan)</th>
                    <th>AU</th>
                    <th>MU</th>
                    <th>SS</th>
                    <th>ROP</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th style="background: rgba(59,130,246,0.08); color: #93c5fd;">Record</th>
                    <th style="background: rgba(59,130,246,0.08); color: #93c5fd;">Qty Total</th>
                    <th style="background: rgba(59,130,246,0.08); color: #93c5fd;">Rentang</th>
                    <th style="background: rgba(16,185,129,0.08); color: #6ee7b7;">Record</th>
                    <th style="background: rgba(16,185,129,0.08); color: #6ee7b7;">Qty Total</th>
                    <th style="background: rgba(16,185,129,0.08); color: #6ee7b7;">Rentang</th>
                    <th></th><th></th><th></th><th></th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($diagnosticData as $row): ?>
                <tr class="<?php echo ($row['has_data'] && $row['au_is_zero']) ? 'highlight-row' : ''; ?>">
                    <td><strong><?php echo $row['product']->kode_produk; ?></strong></td>
                    <td class="text-left"><?php echo htmlspecialchars($row['product']->nama_produk); ?></td>

                    <td><?php echo $row['sale_count']; ?></td>
                    <td><?php echo number_format($row['sale_total']); ?></td>
                    <td style="font-size: 0.7rem;"><?php echo $row['sale_range']; ?></td>

                    <td><?php echo $row['out_count']; ?></td>
                    <td><?php echo number_format($row['out_total']); ?></td>
                    <td style="font-size: 0.7rem;"><?php echo $row['out_range']; ?></td>

                    <?php if ($row['analysis']): ?>
                        <td><?php echo number_format($row['analysis']->average_usage, 2); ?></td>
                        <td><?php echo number_format($row['analysis']->max_sales); ?></td>
                        <td><?php echo number_format($row['analysis']->safety_stock); ?></td>
                        <td><?php echo number_format($row['analysis']->reorder_point); ?></td>
                        <td>
                            <?php if ($row['has_data'] && $row['au_is_zero']): ?>
                                <span class="badge-warn">BUG</span>
                            <?php elseif (!$row['has_data']): ?>
                                <span class="badge-empty">No Data</span>
                            <?php else: ?>
                                <span class="badge-ok">OK</span>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                        <td colspan="5"><span class="badge-empty">Belum Dianalisis</span></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="/analisis-persediaan" class="btn">← Kembali ke Analisis Persediaan</a>

    <footer>
        <p>Diagnostik Analisis Persediaan &copy; 2026. Della Frozen Mart.</p>
    </footer>
</div>
</body>
</html>
