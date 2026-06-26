<?php
/**
 * Laravel 11 Self-Diagnostic Tool
 * Created by Antigravity AI
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to check directory writability
function isWritableSecure($path) {
    if (!file_exists($path)) {
        return false;
    }
    return is_writable($path);
}

// Parse .env file manually
function parseEnv($path) {
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            // Remove quotes
            $val = trim($val, '"\'');
            $config[$key] = $val;
        }
    }
    return $config;
}

// Perform diagnosis
$results = [];
$allPassed = true;

// 1. PHP Version Check
$phpVersion = PHP_VERSION;
$phpPassed = version_compare($phpVersion, '8.2.0', '>=');
$results['php_version'] = [
    'title' => 'Versi PHP Server',
    'value' => 'PHP ' . $phpVersion,
    'status' => $phpPassed ? 'passed' : 'failed',
    'desc' => $phpPassed ? 'Versi PHP sudah sesuai kebutuhan Laravel 11 (minimal PHP 8.2).' : 'Laravel 11 membutuhkan minimal PHP 8.2. Silakan ubah versi PHP di cPanel Anda (MultiPHP Manager / Select PHP Version) menjadi 8.2 atau 8.3.',
];
if (!$phpPassed) $allPassed = false;

// 2. Vendor folder check
$baseDir = dirname(__DIR__);
$autoloadPath = $baseDir . '/vendor/autoload.php';
$vendorExists = file_exists($autoloadPath);
$results['vendor'] = [
    'title' => 'Folder Vendor & Autoload',
    'value' => $vendorExists ? 'Tersedia' : 'Tidak Ditemukan',
    'status' => $vendorExists ? 'passed' : 'failed',
    'desc' => $vendorExists ? 'Folder vendor dan file autoload berhasil dimuat.' : 'Folder "vendor" atau file "vendor/autoload.php" tidak ditemukan! Hal ini biasanya terjadi jika Anda hanya mengunggah file zip kecil tanpa folder vendor. <strong>Solusi:</strong> Kompres folder proyek lokal Anda BESERTA folder "vendor"-nya ke dalam format ZIP, lalu unggah dan ekstrak kembali di File Manager cPanel Anda.',
];
if (!$vendorExists) $allPassed = false;

// 3. Env file check
$envPath = $baseDir . '/.env';
$envExists = file_exists($envPath);
$envConfig = $envExists ? parseEnv($envPath) : [];
$appKeySet = !empty($envConfig['APP_KEY']);

$results['env'] = [
    'title' => 'File Konfigurasi (.env)',
    'value' => $envExists ? 'Tersedia' : 'Tidak Ditemukan',
    'status' => $envExists ? 'passed' : 'failed',
    'desc' => $envExists ? 'File .env ditemukan di direktori utama.' : 'File konfigurasi ".env" tidak ditemukan! <strong>Solusi:</strong> Salin file ".env.example" menjadi ".env" di File Manager cPanel, lalu sesuaikan isinya.',
];
if (!$envExists) $allPassed = false;

$results['app_key'] = [
    'title' => 'Kunci Pengaman (APP_KEY)',
    'value' => $appKeySet ? 'Sudah Diatur' : 'Belum Diatur',
    'status' => ($envExists && $appKeySet) ? 'passed' : 'failed',
    'desc' => ($envExists && $appKeySet) ? 'APP_KEY sudah terkonfigurasi dengan benar.' : 'Kunci pengaman aplikasi belum diatur atau kosong di file .env. <strong>Solusi:</strong> Silakan buka file .env dan isi baris <code>APP_KEY=</code> dengan hash base64 yang valid (misal salin dari komputer lokal Anda).',
];
if (!$appKeySet) $allPassed = false;

// 4. Folder permissions check
$foldersToCheck = [
    'storage' => $baseDir . '/storage',
    'storage/framework' => $baseDir . '/storage/framework',
    'storage/logs' => $baseDir . '/storage/logs',
    'bootstrap/cache' => $baseDir . '/bootstrap/cache',
];
foreach ($foldersToCheck as $name => $path) {
    $exists = file_exists($path);
    $writable = $exists && isWritableSecure($path);
    $results['perm_' . str_replace('/', '_', $name)] = [
        'title' => 'Hak Akses: ' . $name,
        'value' => !$exists ? 'Folder Tidak Ada' : ($writable ? 'Bisa Ditulis (Writable)' : 'Tidak Bisa Ditulis (Read-Only)'),
        'status' => $writable ? 'passed' : 'failed',
        'desc' => $writable ? "Folder $name memiliki hak akses yang benar." : "Folder $name tidak dapat ditulis! <strong>Solusi:</strong> Ubah permission folder ini di cPanel File Manager menjadi 755 atau 775 (klik kanan folder -> Change Permissions). Jika foldernya belum ada, buat foldernya terlebih dahulu.",
    ];
    if (!$writable) $allPassed = false;
}

// 5. PHP Extensions Check
$requiredExtensions = [
    'bcmath', 'ctype', 'fileinfo', 'gd', 'json', 'mbstring', 'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'zip'
];
$missingExts = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExts[] = $ext;
    }
}
$extPassed = empty($missingExts);
$results['extensions'] = [
    'title' => 'Ekstensi PHP',
    'value' => $extPassed ? 'Lengkap' : 'Ada yang Kurang (' . implode(', ', $missingExts) . ')',
    'status' => $extPassed ? 'passed' : 'failed',
    'desc' => $extPassed ? 'Semua ekstensi PHP yang dibutuhkan Laravel 11 sudah aktif.' : 'Ekstensi PHP berikut belum aktif di server Anda: <strong>' . implode(', ', $missingExts) . '</strong>. <strong>Solusi:</strong> Masuk ke cPanel -> Select PHP Version -> Centang ekstensi yang kurang tersebut agar aktif.',
];
if (!$extPassed) $allPassed = false;

// 6. Database Connection Check
$dbStatus = 'Belum Dites';
$dbDesc = 'Koneksi database belum diuji karena file .env tidak tersedia atau bermasalah.';
$dbStatusType = 'failed';

if ($envExists) {
    $dbHost = $envConfig['DB_HOST'] ?? '127.0.0.1';
    $dbPort = $envConfig['DB_PORT'] ?? '3306';
    $dbName = $envConfig['DB_DATABASE'] ?? '';
    $dbUser = $envConfig['DB_USERNAME'] ?? '';
    $dbPass = $envConfig['DB_PASSWORD'] ?? '';
    
    if (empty($dbName) || empty($dbUser)) {
        $dbStatus = 'Konfigurasi Kosong';
        $dbDesc = 'Nama database atau username kosong di file .env. Pastikan Anda telah mengisi konfigurasi database di file .env.';
        $dbStatusType = 'failed';
        $allPassed = false;
    } else {
        try {
            // Try connecting via PDO
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
            ];
            $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
            
            $dbStatus = 'Terhubung!';
            $dbDesc = "Berhasil terhubung ke database <strong>$dbName</strong> secara lancar.";
            $dbStatusType = 'passed';
        } catch (PDOException $e) {
            $dbStatus = 'Gagal Terhubung';
            $dbDesc = 'Koneksi database gagal dengan error: <code style="color:#ef4444;">' . htmlspecialchars($e->getMessage()) . '</code>.<br><strong>Solusi:</strong><br>1. Pastikan Anda telah membuat Database, User Database, dan <strong>menghubungkan User ke Database tersebut</strong> di cPanel -> MySQL Database Wizard.<br>2. Periksa kembali nilai <code>DB_DATABASE</code>, <code>DB_USERNAME</code>, dan <code>DB_PASSWORD</code> pada file .env di server. Ingat bahwa di hosting cPanel, biasanya nama database dan username memiliki prefix username cPanel Anda (contoh: <code>della_frozenmart</code> menjadi <code>namauser_della_frozenmart</code>).';
            $dbStatusType = 'failed';
            $allPassed = false;
        }
    }
}
$results['database'] = [
    'title' => 'Koneksi Database',
    'value' => $dbStatus,
    'status' => $dbStatusType,
    'desc' => $dbDesc,
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 11 - Diagnostic Tool</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-success: #10b981;
            --accent-danger: #ef4444;
            --glass-blur: blur(12px);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 45%);
            background-attachment: fixed;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        header {
            text-align: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }

        header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .summary-status {
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .summary-status.passed {
            background-color: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .summary-status.failed {
            background-color: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge.passed {
            background-color: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .badge.failed {
            background-color: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .card-value {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-family: monospace;
            background: rgba(0, 0, 0, 0.2);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            align-self: flex-start;
        }

        .card-desc {
            font-size: 0.95rem;
            line-height: 1.5;
            color: #cbd5e1;
        }

        .card-desc strong {
            color: #f8fafc;
        }

        .card-desc code {
            background: rgba(239, 68, 68, 0.15);
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
            font-family: monospace;
        }

        footer {
            margin-top: 3rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1.5rem;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1.5rem 1rem;
            }
            header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistem Diagnosa Server</h1>
            <p>Della Frozen Mart Inventory System</p>
        </header>

        <?php if ($allPassed): ?>
            <div class="summary-status passed">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                Semua sistem berjalan normal di server! Jika website Anda masih menampilkan error 500, silakan bersihkan cache web server atau periksa .htaccess.
            </div>
        <?php else: ?>
            <div class="summary-status failed">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Terdeteksi masalah konfigurasi server yang dapat menyebabkan Error 500. Silakan ikuti solusi di bawah ini.
            </div>
        <?php endif; ?>

        <div class="grid">
            <?php foreach ($results as $key => $res): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><?php echo htmlspecialchars($res['title']); ?></span>
                        <span class="badge <?php echo $res['status']; ?>"><?php echo $res['status'] === 'passed' ? 'OK' : 'ERROR'; ?></span>
                    </div>
                    <div class="card-value"><?php echo htmlspecialchars($res['value']); ?></div>
                    <div class="card-desc"><?php echo $res['desc']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer>
            <p>Sistem Diagnosa Persediaan Della Frozen Mart &copy; 2026. Antigravity Coding Assistant.</p>
        </footer>
    </div>
</body>
</html>
