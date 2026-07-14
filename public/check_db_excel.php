<?php
/**
 * Laravel 11 - Excel vs Database Sync Check
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
require $baseDir . '/vendor/autoload.php';

// Bootstrap Laravel safely
if (!isset($app) || !is_object($app)) {
    $app = require $baseDir . '/bootstrap/app.php';
    if ($app === true) {
        // Fallback to app helper if require returned true
        $app = app();
    } else {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }
}

use App\Models\Product;
use App\Models\Sale;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

$filePath = $baseDir . '/data produk/Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx';
if (!file_exists($filePath)) {
    echo "<h1>Error: File Excel Gabungan tidak ditemukan!</h1><p>Path: {$filePath}</p>";
    exit;
}

$spreadsheet = IOFactory::load($filePath);
$sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];

$dbProducts = Product::all();
$dbProductsByName = [];
foreach ($dbProducts as $p) {
    $dbProductsByName[strtolower(trim($p->nama_produk))] = $p;
}

function resolveDbProduct($excelName, $dbProductsByName) {
    $name = trim($excelName);
    $lower = strtolower($name);
    if (isset($dbProductsByName[$lower])) {
        return $dbProductsByName[$lower];
    }
    return null;
}

$janSheet = $spreadsheet->getSheetByName('Januari');
$highestCol = $janSheet->getHighestColumn();
$lastColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

$productColumns = [];
for ($col = 4; $col <= $lastColIdx; $col++) {
    $rawName = $janSheet->getCellByColumnAndRow($col, 3)->getValue();
    if (empty($rawName)) continue;
    $rawName = trim($rawName);
    
    if (in_array(strtolower($rawName), ['total qty', 'total penjualan (rp)', 'total penjualan', 'total'])) {
        continue;
    }
    
    $p = resolveDbProduct($rawName, $dbProductsByName);
    if ($p) {
        $productColumns[$col] = [
            'model' => $p,
            'excel_name' => $rawName,
        ];
    }
}

$excelTotals = [];
foreach ($sheets as $sheetName) {
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) continue;
    
    $highestRow = $sheet->getHighestRow();
    for ($row = 4; $row <= $highestRow; $row++) {
        $dateVal = $sheet->getCell('B' . $row)->getValue();
        if (empty($dateVal)) continue;
        
        $dateStr = null;
        if (is_numeric($dateVal)) {
            try {
                $dateObj = ExcelDate::excelToDateTimeObject($dateVal);
                $dateStr = $dateObj->format('Y-m-d');
            } catch (\Exception $e) {}
        } else {
            try {
                $dateStr = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
            } catch (\Exception $e) {}
        }
        
        if (!$dateStr) continue;
        
        foreach ($productColumns as $colIdx => $info) {
            $prodId = $info['model']->id;
            $qty = $sheet->getCellByColumnAndRow($colIdx, $row)->getValue();
            $qty = ($qty !== null && $qty !== '') ? (int)$qty : 0;
            
            if ($qty > 0) {
                if (!isset($excelTotals[$prodId])) {
                    $excelTotals[$prodId] = [];
                    foreach ($sheets as $m) $excelTotals[$prodId][$m] = 0;
                }
                $excelTotals[$prodId][$sheetName] += $qty;
            }
        }
    }
}

$dbTotals = [];
foreach ($dbProducts as $p) {
    $dbTotals[$p->id] = [];
    foreach ($sheets as $sheetName) {
        $dbTotals[$p->id][$sheetName] = 0;
    }
}

$monthRanges = [
    'Januari' => ['2026-01-01', '2026-01-31'],
    'Februari' => ['2026-02-01', '2026-02-28'],
    'Maret' => ['2026-03-01', '2026-03-31'],
    'April' => ['2026-04-01', '2026-04-30'],
    'Mei' => ['2026-05-01', '2026-05-31'],
];

foreach ($monthRanges as $sheetName => $range) {
    $sales = Sale::whereBetween('tanggal_penjualan', $range)->get();
    foreach ($sales as $sale) {
        if (isset($dbTotals[$sale->product_id])) {
            $dbTotals[$sale->product_id][$sheetName] += $sale->jumlah_terjual;
        }
    }
}

$mismatchCount = 0;
$rowsHtml = "";
foreach ($dbProducts as $idx => $p) {
    $excelSum = isset($excelTotals[$p->id]) ? array_sum($excelTotals[$p->id]) : 0;
    $dbSum = array_sum($dbTotals[$p->id]);
    $diff = $excelSum - $dbSum;
    
    $statusClass = $diff === 0 ? "status-ok" : "status-fail";
    $statusText = $diff === 0 ? "PAS" : "SELISIH";
    
    if ($diff !== 0) {
        $mismatchCount++;
    }
    
    $num = $idx + 1;
    $rowsHtml .= "
    <tr>
        <td style='text-align: center;'>{$num}</td>
        <td><strong>{$p->kode_produk}</strong></td>
        <td>{$p->nama_produk}</td>
        <td style='text-align: right; font-weight: 600;'>{$excelSum}</td>
        <td style='text-align: right; font-weight: 600;'>{$dbSum}</td>
        <td style='text-align: right; font-weight: 700; color: " . ($diff !== 0 ? '#ef4444' : '#10b981') . ";'>" . ($diff > 0 ? "+{$diff}" : $diff) . "</td>
        <td style='text-align: center;'><span class='badge {$statusClass}'>{$statusText}</span></td>
    </tr>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Sinkronisasi Excel vs Sistem</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            margin-bottom: 2rem;
        }
        h1 {
            color: #38bdf8;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
        }
        .card {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        th {
            background-color: #0f172a;
            color: #38bdf8;
            font-weight: 600;
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-ok {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .status-fail {
            background-color: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }
        .alert-box {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Analisis Sinkronisasi Penjualan</h1>
            <p class="subtitle">Membandingkan Gabungan Excel 31 Produk vs Database Penjualan (Januari - Mei 2026)</p>
        </header>

        <?php if ($mismatchCount === 0): ?>
            <div class="alert-box alert-success">
                🎉 <strong>Sinkronisasi Sempurna:</strong> Seluruh data penjualan untuk 31 produk selama 5 bulan pas/sama persis antara file Excel Gabungan dengan Database Sistem!
            </div>
        <?php else: ?>
            <div class="alert-box alert-danger">
                ⚠️ <strong>Perhatian:</strong> Ditemukan <?php echo $mismatchCount; ?> produk yang memiliki perbedaan data antara file Excel dengan Database.
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Tabel Perbandingan Penjualan Produk</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 120px;">Kode Produk</th>
                        <th>Nama Produk</th>
                        <th style="text-align: right; width: 150px;">Total Qty Excel</th>
                        <th style="text-align: right; width: 150px;">Total Qty Sistem</th>
                        <th style="text-align: right; width: 120px;">Selisih</th>
                        <th style="text-align: center; width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $rowsHtml; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
