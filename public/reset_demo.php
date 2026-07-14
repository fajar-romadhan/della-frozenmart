<?php
/**
 * Laravel 11 Database Demo Reset & Seeder Tool
 * Created by Antigravity AI
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check security key from .env or fallback
$envFile = dirname(__DIR__) . '/.env';
$secureKey = 'DellaFrozenMart2026_SecureKey'; // fallback
$appEnv = 'production';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (preg_match('/^DEMO_RESET_KEY=(.*)$/m', $envContent, $matches)) {
        $secureKey = trim($matches[1], "\"' ");
    }
    if (preg_match('/^APP_ENV=(.*)$/m', $envContent, $matches)) {
        $appEnv = trim($matches[1], "\"' ");
    }
}

// Request must have key in query string (for GET) or POST param (for form submission)
$providedKey = $_REQUEST['key'] ?? '';

if ($providedKey !== $secureKey) {
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

// Check if vendor folder exists
$vendorExists = file_exists($baseDir . '/vendor/autoload.php');

$message = '';
$messageType = '';
$logOutput = '';

if ($vendorExists && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        // Bootstrap Laravel safely
        require $baseDir . '/vendor/autoload.php';
        if (!isset($app) || !is_object($app)) {
            $app = require $baseDir . '/bootstrap/app.php';
            if ($app === true) {
                $app = app();
            } else {
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();
            }
        }
        
        if ($action === 'clear') {
            // Fresh migration to empty all tables
            $exitCode = Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
            $logOutput .= "1. Refresh Database (Mengosongkan Semua Tabel): " . ($exitCode === 0 ? "SUKSES (OK)\n" : "GAGAL (Code: $exitCode)\n");
            
            // Seed ONLY users
            $seedExitCode = Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\UserSeeder',
                '--force' => true
            ]);
            $logOutput .= "2. Membuat Akun Login Default (Admin, Manager, Owner): " . ($seedExitCode === 0 ? "SUKSES (OK)\n" : "GAGAL (Code: $seedExitCode)\n");
            
            // Explicitly truncate tables that might have been populated by revision migrations (e.g. 11 products revision)
            try {
                Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Illuminate\Support\Facades\DB::table('detail_barang_keluar')->truncate();
                Illuminate\Support\Facades\DB::table('barang_keluar')->truncate();
                Illuminate\Support\Facades\DB::table('batch_stok')->truncate();
                Illuminate\Support\Facades\DB::table('barang_masuk')->truncate();
                Illuminate\Support\Facades\DB::table('produk')->truncate();
                Illuminate\Support\Facades\DB::table('supplier')->truncate();
                Illuminate\Support\Facades\DB::table('kategori')->truncate();
                Illuminate\Support\Facades\DB::table('stok_opname')->truncate();
                Illuminate\Support\Facades\DB::table('penjualan')->truncate();
                Illuminate\Support\Facades\DB::table('pemesanan_supplier')->truncate();
                Illuminate\Support\Facades\DB::table('analisa_persediaan')->truncate();
                Illuminate\Support\Facades\DB::table('notifikasi')->truncate();
                Illuminate\Support\Facades\DB::table('log_import')->truncate();
                Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                // Seed default categories
                Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\CategorySeeder',
                    '--force' => true
                ]);

                // Seed the 31 clean products list with 0 stock
                Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\ProductSeeder',
                    '--force' => true
                ]);

                $logOutput .= "2b. Membersihkan data bawaan migrasi revisi & memuat 31 Produk (0 Stok): SUKSES (OK)\n";
            } catch (\Exception $e) {
                Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $logOutput .= "2b. Membersihkan data bawaan migrasi revisi & memuat 31 Produk: GAGAL (" . $e->getMessage() . ")\n";
            }

            // Clear caches
            Illuminate\Support\Facades\Artisan::call('cache:clear');
            Illuminate\Support\Facades\Artisan::call('config:clear');
            $logOutput .= "3. Pembersihan Cache Aplikasi: SUKSES (OK)\n";
            
            if ($exitCode === 0 && $seedExitCode === 0) {
                $message = "Database berhasil dikosongkan! Sekarang sistem bersih dari data produk, kategori, supplier, dan transaksi. Hanya akun login default yang aktif.";
                $messageType = 'success';
            } else {
                $message = "Terjadi kegagalan saat membersihkan database.";
                $messageType = 'error';
            }
        } elseif ($action === 'seed') {
            // Fresh migration
            $exitCode = Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
            $logOutput .= "1. Refresh Database: " . ($exitCode === 0 ? "SUKSES (OK)\n" : "GAGAL (Code: $exitCode)\n");
            
            // Seed ALL data
            $seedExitCode = Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $logOutput .= "2. Mengisi Seluruh Data Contoh (Full Seed): " . ($seedExitCode === 0 ? "SUKSES (OK)\n" : "GAGAL (Code: $seedExitCode)\n");
            
            // Clear caches
            Illuminate\Support\Facades\Artisan::call('cache:clear');
            Illuminate\Support\Facades\Artisan::call('config:clear');
            $logOutput .= "3. Pembersihan Cache Aplikasi: SUKSES (OK)\n";
            
            if ($exitCode === 0 && $seedExitCode === 0) {
                $message = "Data contoh berhasil dimasukkan kembali! Seluruh produk, kategori, supplier, batch stok, dan transaksi contoh telah dipulihkan.";
                $messageType = 'success';
            } else {
                $message = "Terjadi kegagalan saat memasukkan data contoh.";
                $messageType = 'error';
            }
        } elseif ($action === 'reset_stock') {
            // Disable foreign key checks
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Truncate transaction and log tables
            Illuminate\Support\Facades\DB::table('barang_masuk')->truncate();
            Illuminate\Support\Facades\DB::table('barang_keluar')->truncate();
            Illuminate\Support\Facades\DB::table('detail_barang_keluar')->truncate();
            Illuminate\Support\Facades\DB::table('batch_stok')->truncate();
            Illuminate\Support\Facades\DB::table('stok_opname')->truncate();
            Illuminate\Support\Facades\DB::table('penjualan')->truncate();
            Illuminate\Support\Facades\DB::table('pemesanan_supplier')->truncate();
            Illuminate\Support\Facades\DB::table('analisa_persediaan')->truncate();
            Illuminate\Support\Facades\DB::table('notifikasi')->truncate();
            Illuminate\Support\Facades\DB::table('log_import')->truncate();
            
            // Reset stok_saat_ini on produk table
            Illuminate\Support\Facades\DB::table('produk')->update(['stok_saat_ini' => 0]);
            
            // Re-enable foreign key checks
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Clear caches
            Illuminate\Support\Facades\Artisan::call('cache:clear');
            Illuminate\Support\Facades\Artisan::call('config:clear');
            
            $logOutput .= "1. Mengosongkan Tabel Barang Masuk (barang_masuk): SUKSES\n";
            $logOutput .= "2. Mengosongkan Tabel Barang Keluar (barang_keluar): SUKSES\n";
            $logOutput .= "3. Mengosongkan Tabel Detail Barang Keluar (detail_barang_keluar): SUKSES\n";
            $logOutput .= "4. Mengosongkan Tabel Batch Stok (batch_stok): SUKSES\n";
            $logOutput .= "5. Mengosongkan Tabel Stock Opname (stok_opname): SUKSES\n";
            $logOutput .= "6. Mengosongkan Tabel Penjualan (penjualan): SUKSES\n";
            $logOutput .= "7. Mengosongkan Tabel Pemesanan Supplier (pemesanan_supplier): SUKSES\n";
            $logOutput .= "8. Mengosongkan Tabel Analisa Persediaan (analisa_persediaan): SUKSES\n";
            $logOutput .= "9. Mengosongkan Tabel Notifikasi (notifikasi): SUKSES\n";
            $logOutput .= "10. Mengosongkan Tabel Log Import (log_import): SUKSES\n";
            $logOutput .= "11. Mereset stok_saat_ini seluruh Produk ke 0: SUKSES\n";
            $logOutput .= "12. Pembersihan Cache Aplikasi: SUKSES\n";
            
            $message = "Seluruh stok produk berhasil di-reset menjadi 0! Data produk, kategori, supplier, dan pengguna tetap aman.";
            $messageType = 'success';
        } elseif ($action === 'import_sales') {
            // Import sales from Della_FrozenMart_Jan-Mei_2026_Final.xlsx
            $excelFile = $baseDir . '/Della_FrozenMart_Jan-Mei_2026_Final.xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel penjualan tidak ditemukan di server: " . $excelFile);
            }
            
            // Disable foreign key checks temporarily and truncate penjualan and safety stock analyses
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Illuminate\Support\Facades\DB::table('penjualan')->truncate();
            Illuminate\Support\Facades\DB::table('analisa_persediaan')->truncate();
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Load file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            $productMapping = [
                // Kolom Excel => Nama produk PERSIS sesuai ProductSeeder & 31 produk resmi
                'D' => 'Okey Sosis 500GR',         // Excel col D: Okey Sosis 500GR  → PRD-0011
                'E' => 'Fiesta Chicken Nugget 450GR',    // Excel col E: Fiesta Nugget 450gr → PRD-0028
                'F' => 'Jamur Enoki',             // Excel col F: Jamur Enoki         → PRD-0012
                'G' => 'Meru Lapis Bogor',        // Excel col G: Meru Lapis Bogor    → PRD-0022
                'H' => 'Okey Nugget Stik 500GR',   // Excel col H: Okey Nugget Stik 500GR → PRD-0005
                'I' => 'Cireng Rujak',            // Excel col I: Cireng Rujak         → PRD-0008
                'J' => 'Salam Nugget 500GR',       // Excel col J: Salam Nugget 500gr  → PRD-0026
                'K' => 'Warisan Isi 50',          // Excel col K: Warisan Isi 50        → PRD-0018
                'L' => 'Belfood Sosis Isi 30',     // Excel col L: Belfood Sosis Isi 30 → PRD-0030
                'M' => 'Richeese Nugget',          // Excel col M: Richeese Nugget      → PRD-0023
            ];
            
            $productsCache = [];
            foreach ($productMapping as $col => $name) {
                $p = \App\Models\Product::where('nama_produk', $name)->first();
                if ($p) {
                    $productsCache[$col] = $p->id;
                } else {
                    $logOutput .= "Peringatan: Produk '$name' tidak ditemukan di database!\n";
                }
            }
            
            $insertCount = 0;
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) {
                    $logOutput .= "Peringatan: Sheet '$sheetName' tidak ditemukan!\n";
                    continue;
                }
                
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $dateVal = $sheet->getCell('B' . $row)->getValue();
                    if (empty($dateVal)) {
                        continue;
                    }
                    
                    // Convert Excel serial date to PHP DateTime
                    if (is_numeric($dateVal)) {
                        $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal);
                        $dateStr = $parsedDate->format('Y-m-d');
                    } else {
                        try {
                            $dateStr = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            continue; // skip invalid date
                        }
                    }
                    
                    foreach ($productMapping as $col => $name) {
                        if (!isset($productsCache[$col])) {
                            continue;
                        }
                        
                        $qty = $sheet->getCell($col . $row)->getValue();
                        $qty = ($qty !== null && $qty !== '') ? (int) $qty : 0;
                        
                        if ($qty > 0) {
                            \App\Models\Sale::create([
                                'product_id' => $productsCache[$col],
                                'tanggal_penjualan' => $dateStr,
                                'jumlah_terjual' => $qty,
                                'sumber_import' => 'Excel',
                                'nama_file_import' => 'Della_FrozenMart_Jan-Mei_2026_Final.xlsx',
                                'user_id' => 1,
                            ]);
                            $insertCount++;
                        }
                    }
                }
            }
            
            // Trigger recalculation of Safety Stock / ROP for these 10 products
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount = 0;
            
            foreach ($productsCache as $col => $prodId) {
                $product = \App\Models\Product::find($prodId);
                if ($product) {
                    $safetyStockService->calculate($product, 3, '2026-01-01', '2026-05-31');
                    $recalculatedCount++;
                }
            }
            
            $logOutput .= "1. Import data penjualan dari Excel: Berhasil ($insertCount baris dimasukkan)\n";
            $logOutput .= "2. Kalkulasi ROP & Safety Stock untuk 10 produk: Berhasil ($recalculatedCount produk dianalisis)\n";
            $message = "Sukses mengimpor data penjualan 5 bulan untuk 10 produk dari file Excel! ROP & Safety Stock otomatis dikalkulasi.";
            $messageType = "success";
        } elseif ($action === 'import_remaining_sales') {
            $excelFile = $baseDir . '/Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel penjualan sisa tidak ditemukan di server: " . $excelFile);
            }
            
            // Delete previously imported sales from this file to prevent duplicates (make it idempotent)
            Illuminate\Support\Facades\DB::table('penjualan')
                ->where('nama_file_import', 'Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx')
                ->delete();
            
            // Load file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            
            // We will dynamically map columns by reading Row 3 (headings) of the first sheet
            $firstSheet = $spreadsheet->getSheetByName($sheets[0]);
            if (!$firstSheet) {
                throw new Exception("Sheet 'Januari' tidak ditemukan di file Excel!");
            }
            
            $productMapping = []; // Excel col letter => Product name in DB
            $productsCache = [];  // Excel col letter => Product ID in DB
            
            $highestColumn = $firstSheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Columns start from 'D' (index 4) up to the highest column
            for ($colIndex = 4; $colIndex <= $highestColumnIndex; $colIndex++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $productName = $firstSheet->getCell($colLetter . '3')->getValue();
                
                if (empty($productName)) {
                    continue;
                }
                
                $productName = trim($productName);
                if (in_array(strtolower($productName), ['total qty', 'total penjualan (rp)', 'total penjualan'])) {
                    continue;
                }
                
                // Skip the 2 products that are not in the 31 official products:
                if (in_array(strtolower($productName), ['spicy chicken wings 1 kg', 'sosis bakar ayam 500g'])) {
                    $logOutput .= "Info: Produk baru '$productName' diabaikan secara otomatis (skip) agar tetap 31 produk.\n";
                    continue;
                }
                
                $dbName = $productName;
                
                $product = \App\Models\Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($dbName)])->first();
                
                if ($product) {
                    $productMapping[$colLetter] = $product->nama_produk;
                    $productsCache[$colLetter] = $product->id;
                    $logOutput .= "Peta: Kolom $colLetter -> '" . $product->nama_produk . "' (ID: " . $product->id . ")\n";
                } else {
                    $logOutput .= "Peringatan: Produk '$productName' di Excel tidak ditemukan di database!\n";
                }
            }
            
            $insertCount = 0;
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) {
                    $logOutput .= "Peringatan: Sheet '$sheetName' tidak ditemukan!\n";
                    continue;
                }
                
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $dateVal = $sheet->getCell('B' . $row)->getValue();
                    if (empty($dateVal)) {
                        continue;
                    }
                    
                    // Convert Excel serial date to PHP DateTime
                    if (is_numeric($dateVal)) {
                        $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal);
                        $dateStr = $parsedDate->format('Y-m-d');
                    } else {
                        try {
                            $dateStr = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            continue; // skip invalid date
                        }
                    }
                    
                    foreach ($productsCache as $col => $prodId) {
                        $qty = $sheet->getCell($col . $row)->getValue();
                        $qty = ($qty !== null && $qty !== '') ? (int) $qty : 0;
                        
                        if ($qty > 0) {
                            \App\Models\Sale::create([
                                'product_id' => $prodId,
                                'tanggal_penjualan' => $dateStr,
                                'jumlah_terjual' => $qty,
                                'sumber_import' => 'Excel Tambahan',
                                'nama_file_import' => 'Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx',
                                'user_id' => 1,
                            ]);
                            $insertCount++;
                        }
                    }
                }
            }
            
            // Trigger recalculation of Safety Stock / ROP for these 22 products
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount = 0;
            
            foreach ($productsCache as $col => $prodId) {
                $product = \App\Models\Product::find($prodId);
                if ($product) {
                    $safetyStockService->calculate($product, 3, '2026-01-01', '2026-05-31');
                    $recalculatedCount++;
                }
            }
            
            $logOutput .= "1. Import data penjualan sisa dari Excel: Berhasil ($insertCount baris dimasukkan)\n";
            $logOutput .= "2. Kalkulasi ROP & Safety Stock untuk 22 produk: Berhasil ($recalculatedCount produk dianalisis)\n";
            $message = "Sukses mengimpor data penjualan sisa 5 bulan untuk 22 produk tambahan! ROP & Safety Stock otomatis dikalkulasi.";
            $messageType = "success";
        } elseif ($action === 'import_incoming_goods') {
            $excelFile = $baseDir . '/DATA_SIMULASI_BARANG_MASUK_JAN-MEI_2026 (1).xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel barang masuk tidak ditemukan di server: " . $excelFile);
            }
            
            // Perform rollback of previously imported items from this file to prevent duplicates
            $oldGoods = Illuminate\Support\Facades\DB::table('barang_masuk')
                ->where('nama_file_import', 'DATA_SIMULASI_BARANG_MASUK_JAN-MEI_2026 (1).xlsx')
                ->get();
                
            $logOutput .= "Rollback: Ditemukan " . count($oldGoods) . " barang masuk lama dari file ini. Memulai pembersihan...\n";
            
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($oldGoods as $og) {
                // Decrement stock in produk table
                Illuminate\Support\Facades\DB::table('produk')
                    ->where('id', $og->product_id)
                    ->decrement('stok_saat_ini', $og->jumlah);
                // Delete stock batch
                Illuminate\Support\Facades\DB::table('batch_stok')
                    ->where('incoming_good_id', $og->id)
                    ->delete();
            }
            // Delete incoming goods
            Illuminate\Support\Facades\DB::table('barang_masuk')
                ->where('nama_file_import', 'DATA_SIMULASI_BARANG_MASUK_JAN-MEI_2026 (1).xlsx')
                ->delete();
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $logOutput .= "Rollback: Pembersihan selesai.\n";

            // Load file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            
            // Build cache maps for products and suppliers
            $products = \App\Models\Product::all();
            $productsCache = [];
            foreach ($products as $p) {
                $productsCache[strtolower(trim($p->nama_produk))] = $p;
            }
            // Add mapping for typo/spelling
            if (isset($productsCache[strtolower('Champ Nugget KombinasiI 450GR')])) {
                $productsCache[strtolower('Champ Nugget Kombinasi 450GR')] = $productsCache[strtolower('Champ Nugget KombinasiI 450GR')];
            }
            
            $suppliersCache = [];
            $suppliers = \App\Models\Supplier::all();
            foreach ($suppliers as $s) {
                $suppliersCache[strtolower(trim($s->nama_supplier))] = $s->id;
            }
            
            // Specific location map for Snack Frozen
            $snackLocationMap = [
                strtolower('Fiesta Kentang 500 gr') => 'RAK-A',
                strtolower('Kentang Goreng 500 gram') => 'RAK-B',
                strtolower('Onion Ring Frozen 250g') => 'RAK-C',
                strtolower('Onion Ring Frozen 500g') => 'RAK-D',
                strtolower('WARISAN ISI 25') => 'RAK-A',
            ];
            
            $adminUser = Illuminate\Support\Facades\DB::table('pengguna')->orderBy('id')->first();
            $userId = $adminUser ? $adminUser->id : 1;
            
            $insertCount = 0;
            $skippedCount = 0;
            $todayCounts = [];
            $productsToRecalculate = [];
            
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) {
                    $logOutput .= "Peringatan: Sheet '$sheetName' tidak ditemukan!\n";
                    continue;
                }
                
                $highestRow = $sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $productName = $sheet->getCell('B' . $row)->getValue();
                    $supplierName = $sheet->getCell('C' . $row)->getValue();
                    
                    if (empty($productName) || empty($supplierName)) {
                        continue;
                    }
                    
                    $productName = trim($productName);
                    $supplierName = trim($supplierName);
                    
                    // Skip products not in the 31 official ones
                    if (in_array(strtolower($productName), ['fiesta karage 450 gr', 'sosis bakar ayam 500g', 'spicy chicken wings 1 kg'])) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // Match product
                    $product = $productsCache[strtolower($productName)] ?? null;
                    if (!$product) {
                        $logOutput .= "Peringatan: Produk '$productName' di Excel tidak terdaftar di database! Dilewati.\n";
                        continue;
                    }
                    
                    // Match or create supplier
                    $supplierKey = strtolower($supplierName);
                    if (!isset($suppliersCache[$supplierKey])) {
                        $newSup = \App\Models\Supplier::create([
                            'nama_supplier' => $supplierName,
                            'status_aktif' => true
                        ]);
                        $suppliersCache[$supplierKey] = $newSup->id;
                        $logOutput .= "Supplier: Membuat supplier baru '$supplierName'\n";
                    }
                    $supplierId = $suppliersCache[$supplierKey];
                    
                    // Read date, qty, and price
                    $dateMasukVal = $sheet->getCell('E' . $row)->getValue(); // tanggal masuk
                    $qtyVal = $sheet->getCell('G' . $row)->getValue();
                    $priceVal = $sheet->getCell('H' . $row)->getValue();
                    
                    // Parse tanggal masuk
                    if (is_numeric($dateMasukVal)) {
                        $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateMasukVal);
                        $dateStr = $parsedDate->format('Y-m-d');
                    } else {
                        try {
                            $dateStr = \Carbon\Carbon::parse($dateMasukVal)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            continue; // skip invalid date
                        }
                    }
                    
                    $qty = (int)$qtyVal;
                    $price = (float)$priceVal;
                    
                    if ($qty <= 0) {
                        continue;
                    }
                    
                    // Determine storage location based on category
                    $catId = (int)$product->category_id;
                    $idLokasi = 'RAK-A'; // default
                    
                    if ($catId === 1) { // Frozen Food
                        $idLokasi = 'FRZ-01';
                    } elseif ($catId === 2 || $catId === 3) { // Seafood / Daging
                        $idLokasi = 'FRZ-02';
                    } elseif ($catId === 4) { // Ayam
                        $idLokasi = 'FRZ-03';
                    } elseif ($catId === 5) { // Snack Frozen
                        $pKey = strtolower($product->nama_produk);
                        $idLokasi = $snackLocationMap[$pKey] ?? 'RAK-A';
                    }
                    
                    // Generate unique batch code BM-YYYYMMDD-XXXX
                    $dateKey = str_replace('-', '', $dateStr);
                    if (!isset($todayCounts[$dateStr])) {
                        $todayCounts[$dateStr] = \App\Models\IncomingGood::whereDate('tanggal_masuk', $dateStr)->count();
                    }
                    $todayCounts[$dateStr]++;
                    $batchCode = 'BM-' . $dateKey . '-' . str_pad($todayCounts[$dateStr], 4, '0', STR_PAD_LEFT);
                    
                    // Expiry date default: 6 months after tanggal_masuk
                    $expiry = \Carbon\Carbon::parse($dateStr)->addMonths(6)->toDateString();
                    
                    // Create incoming good
                    $incomingGood = \App\Models\IncomingGood::create([
                        'product_id' => $product->id,
                        'supplier_id' => $supplierId,
                        'tanggal_masuk' => $dateStr,
                        'jumlah' => $qty,
                        'satuan' => $product->satuan,
                        'harga_beli' => $price,
                        'tanggal_kedaluwarsa' => $expiry,
                        'batch_code' => $batchCode,
                        'sumber_import' => 'Excel Barang Masuk',
                        'nama_file_import' => 'DATA_SIMULASI_BARANG_MASUK_JAN-MEI_2026 (1).xlsx',
                        'id_lokasi' => $idLokasi,
                        'keterangan' => 'Import Otomatis Data Jan-Mei 2026',
                        'user_id' => $userId,
                    ]);
                    
                    // Create Stock Batch
                    \App\Models\StockBatch::create([
                        'product_id' => $product->id,
                        'incoming_good_id' => $incomingGood->id,
                        'batch_code' => $batchCode,
                        'tanggal_masuk' => $dateStr,
                        'tanggal_kedaluwarsa' => $expiry,
                        'jumlah_awal' => $qty,
                        'jumlah_sisa' => $qty,
                        'satuan' => $product->satuan,
                    ]);
                    
                    // Increment product stock
                    $product->increment('stok_saat_ini', $qty);
                    $productsToRecalculate[$product->id] = $product;
                    
                    $insertCount++;
                }
            }
            
            // Recalculate ROP / Safety Stock
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount = 0;
            foreach ($productsToRecalculate as $p) {
                $safetyStockService->calculate($p);
                $recalculatedCount++;
            }
            
            $logOutput .= "1. Impor data barang masuk dari Excel: Berhasil ($insertCount baris dimasukkan, $skippedCount produk non-resmi dilewati)\n";
            $logOutput .= "2. Kalkulasi ROP & Safety Stock: Berhasil ($recalculatedCount produk dianalisis)\n";
            $message = "Sukses mengimpor data barang masuk 5 bulan untuk 22 produk! Lokasi penyimpanan terpetakan secara otomatis dan stok berhasil diperbarui.";
            $messageType = "success";
        } elseif ($action === 'import_remaining_outgoing') {
            $excelFile = $baseDir . '/Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel penjualan sisa tidak ditemukan di server: " . $excelFile);
            }
            
            // Delete previously imported outgoing goods and details to prevent duplicates (make it idempotent)
            $oldOutgoings = Illuminate\Support\Facades\DB::table('barang_keluar')
                ->where('keterangan', 'Dari import barang keluar sisa')
                ->get();
                
            $logOutput .= "Rollback: Ditemukan " . count($oldOutgoings) . " barang keluar lama dari file ini. Memulai pemulihan stok...\n";
            
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($oldOutgoings as $oo) {
                // Restore stock back to produk
                Illuminate\Support\Facades\DB::table('produk')
                    ->where('id', $oo->product_id)
                    ->increment('stok_saat_ini', $oo->jumlah);
                    
                // Restore batch_stok levels from detail_barang_keluar
                $details = Illuminate\Support\Facades\DB::table('detail_barang_keluar')
                    ->where('outgoing_good_id', $oo->id)
                    ->get();
                foreach ($details as $d) {
                    Illuminate\Support\Facades\DB::table('batch_stok')
                        ->where('id', $d->stock_batch_id)
                        ->increment('jumlah_sisa', $d->jumlah_diambil);
                }
                
                // Delete details
                Illuminate\Support\Facades\DB::table('detail_barang_keluar')
                    ->where('outgoing_good_id', $oo->id)
                    ->delete();
            }
            Illuminate\Support\Facades\DB::table('barang_keluar')
                ->where('keterangan', 'Dari import barang keluar sisa')
                ->delete();
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $logOutput .= "Rollback: Pemulihan stok selesai.\n";
            
            // Load file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            
            // We will dynamically map columns by reading Row 3 (headings) of the first sheet
            $firstSheet = $spreadsheet->getSheetByName($sheets[0]);
            if (!$firstSheet) {
                throw new Exception("Sheet 'Januari' tidak ditemukan di file Excel!");
            }
            
            $productMapping = []; // Excel col letter => Product name in DB
            $productsCache = [];  // Excel col letter => Product ID in DB
            
            $highestColumn = $firstSheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Build products cache maps
            $products = \App\Models\Product::all();
            $dbProductsCache = [];
            foreach ($products as $p) {
                $dbProductsCache[strtolower(trim($p->nama_produk))] = $p->id;
            }
            
            for ($colIndex = 4; $colIndex <= $highestColumnIndex; $colIndex++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $productName = $firstSheet->getCell($colLetter . '3')->getValue();
                
                if (empty($productName)) {
                    continue;
                }
                
                $productName = trim($productName);
                if (in_array(strtolower($productName), ['total qty', 'total penjualan (rp)', 'total penjualan'])) {
                    continue;
                }
                
                // Skip the 2 products that are not in the 31 official products:
                if (in_array(strtolower($productName), ['spicy chicken wings 1 kg', 'sosis bakar ayam 500g'])) {
                    continue;
                }
                
                // Find matching product in database
                $prodId = $dbProductsCache[strtolower($productName)] ?? null;
                if ($prodId) {
                    $productMapping[$colLetter] = $productName;
                    $productsCache[$colLetter] = $prodId;
                }
            }
            
            $adminUser = Illuminate\Support\Facades\DB::table('pengguna')->orderBy('id')->first();
            $userId = $adminUser ? $adminUser->id : 1;
            
            $insertCount = 0;
            $productsToRecalculate = [];
            
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) {
                    continue;
                }
                
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $dateVal = $sheet->getCell('B' . $row)->getValue();
                    if (empty($dateVal)) {
                        continue;
                    }
                    
                    // Convert Excel serial date to PHP DateTime
                    if (is_numeric($dateVal)) {
                        $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal);
                        $dateStr = $parsedDate->format('Y-m-d');
                    } else {
                        try {
                            $dateStr = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            continue; // skip invalid date
                        }
                    }
                    
                    foreach ($productMapping as $col => $name) {
                        if (!isset($productsCache[$col])) {
                            continue;
                        }
                        
                        $qty = $sheet->getCell($col . $row)->getValue();
                        $qty = ($qty !== null && $qty !== '') ? (int) $qty : 0;
                        
                        if ($qty > 0) {
                            $prodId = $productsCache[$col];
                            
                            $outgoingGood = \App\Models\OutgoingGood::create([
                                'product_id' => $prodId,
                                'tanggal_keluar' => $dateStr,
                                'jumlah' => $qty,
                                'jenis_keluar' => 'penjualan',
                                'keterangan' => 'Dari import barang keluar sisa',
                                'user_id' => $userId,
                            ]);
                            
                            // FIFO deduction
                            $batches = \App\Models\StockBatch::where('product_id', $prodId)
                                ->where('jumlah_sisa', '>', 0)
                                ->orderByRaw('CASE WHEN tanggal_kedaluwarsa IS NULL THEN 1 ELSE 0 END')
                                ->orderBy('tanggal_kedaluwarsa', 'asc')
                                ->orderBy('tanggal_masuk', 'asc')
                                ->get();
                                
                            $remaining = $qty;
                            foreach ($batches as $batch) {
                                if ($remaining <= 0) {
                                    break;
                                }
                                $take = min($remaining, $batch->jumlah_sisa);
                                
                                \App\Models\OutgoingGoodDetail::create([
                                    'outgoing_good_id' => $outgoingGood->id,
                                    'stock_batch_id' => $batch->id,
                                    'jumlah_diambil' => $take,
                                ]);
                                
                                $batch->decrement('jumlah_sisa', $take);
                                $remaining -= $take;
                            }
                            
                            // Deficit support (goes negative on oldest batch)
                            if ($remaining > 0) {
                                $firstBatch = \App\Models\StockBatch::where('product_id', $prodId)
                                    ->orderBy('tanggal_masuk', 'asc')
                                    ->first();
                                if ($firstBatch) {
                                    \App\Models\OutgoingGoodDetail::create([
                                        'outgoing_good_id' => $outgoingGood->id,
                                        'stock_batch_id' => $firstBatch->id,
                                        'jumlah_diambil' => $remaining,
                                    ]);
                                    $firstBatch->decrement('jumlah_sisa', $remaining);
                                }
                            }
                            
                            // Decrement product stock directly
                            $product = \App\Models\Product::find($prodId);
                            if ($product) {
                                $product->decrement('stok_saat_ini', $qty);
                                $productsToRecalculate[$prodId] = $product;
                            }
                            
                            $insertCount++;
                        }
                    }
                }
            }
            
            // Recalculate Safety Stock / ROP
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount = 0;
            foreach ($productsToRecalculate as $p) {
                $safetyStockService->calculate($p);
                $recalculatedCount++;
            }
            
            $logOutput .= "1. Impor data barang keluar dari Excel: Berhasil ($insertCount baris dimasukkan)\n";
            $logOutput .= "2. Kalkulasi ROP & Safety Stock: Berhasil ($recalculatedCount produk dianalisis)\n";
            $message = "Sukses mengimpor dan menyinkronkan data barang keluar sisa 5 bulan untuk 22 produk tambahan dengan metode FIFO!";
            $messageType = "success";
        } elseif ($action === 'import_combined_sales') {
            $excelFile = $baseDir . '/data produk/Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel penjualan gabungan tidak ditemukan di server: " . $excelFile);
            }
            
            // Truncate penjualan and safety stock analyses
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Illuminate\Support\Facades\DB::table('penjualan')->truncate();
            Illuminate\Support\Facades\DB::table('analisa_persediaan')->truncate();
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Load file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            
            // Retrieve all products
            $dbProducts = \App\Models\Product::all();
            $dbProductsByName = [];
            foreach ($dbProducts as $p) {
                $dbProductsByName[strtolower(trim($p->nama_produk))] = $p;
            }
            
            // We map columns from the 'Januari' sheet Row 3
            $janSheet = $spreadsheet->getSheetByName('Januari');
            if (!$janSheet) {
                throw new Exception("Sheet 'Januari' tidak ditemukan!");
            }
            
            $highestColumn = $janSheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            $productColumns = []; // Col Letter => Product ID
            $resolvedNames = [];
            
            for ($colIndex = 4; $colIndex <= $highestColumnIndex; $colIndex++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $productName = $janSheet->getCell($colLetter . '3')->getValue();
                
                if (empty($productName)) {
                    continue;
                }
                
                $productName = trim($productName);
                if (in_array(strtolower($productName), ['total qty', 'total penjualan (rp)', 'total penjualan', 'total'])) {
                    continue;
                }
                
                $dbName = $productName;
                
                $product = \App\Models\Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($dbName)])->first();
                if ($product) {
                    $productColumns[$colLetter] = $product->id;
                    $resolvedNames[$colLetter] = $product->nama_produk;
                } else {
                    $logOutput .= "Peringatan: Produk '$productName' di Excel tidak ditemukan di database!\n";
                }
            }
            
            $insertCount = 0;
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) {
                    $logOutput .= "Peringatan: Sheet '$sheetName' tidak ditemukan!\n";
                    continue;
                }
                
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $dateVal = $sheet->getCell('B' . $row)->getValue();
                    if (empty($dateVal)) {
                        continue;
                    }
                    
                    // Convert Excel serial date to PHP DateTime
                    if (is_numeric($dateVal)) {
                        $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal);
                        $dateStr = $parsedDate->format('Y-m-d');
                    } else {
                        try {
                            $dateStr = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            continue; // skip invalid date
                        }
                    }
                    
                    foreach ($productColumns as $col => $prodId) {
                        $qty = $sheet->getCell($col . $row)->getValue();
                        $qty = ($qty !== null && $qty !== '') ? (int) $qty : 0;
                        
                        if ($qty > 0) {
                            \App\Models\Sale::create([
                                'product_id' => $prodId,
                                'tanggal_penjualan' => $dateStr,
                                'jumlah_terjual' => $qty,
                                'sumber_import' => 'Excel Gabungan',
                                'nama_file_import' => 'Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx',
                                'user_id' => 1,
                            ]);
                            $insertCount++;
                        }
                    }
                }
            }
            
            // Trigger recalculation of Safety Stock / ROP for all products
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount = 0;
            
            $uniqueProductIds = array_unique(array_values($productColumns));
            foreach ($uniqueProductIds as $prodId) {
                $product = \App\Models\Product::find($prodId);
                if ($product) {
                    $safetyStockService->calculate($product, 3, '2026-01-01', '2026-05-31');
                    $recalculatedCount++;
                }
            }
            
            $logOutput .= "1. Import data penjualan gabungan dari Excel: Berhasil ($insertCount baris dimasukkan)\n";
            $logOutput .= "2. Kalkulasi ROP & Safety Stock untuk seluruh produk: Berhasil ($recalculatedCount produk dianalisis)\n";
            $message = "Sukses mengimpor data penjualan 5 bulan untuk 31 produk dari Excel Gabungan! ROP & Safety Stock otomatis dikalkulasi.";
            $messageType = "success";
        } elseif ($action === 'full_reset_demo') {
            // ─── STEP 1: Wipe all transaction data ───────────────────────────────
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Illuminate\Support\Facades\DB::table('detail_barang_keluar')->truncate();
            Illuminate\Support\Facades\DB::table('barang_keluar')->truncate();
            Illuminate\Support\Facades\DB::table('batch_stok')->truncate();
            Illuminate\Support\Facades\DB::table('barang_masuk')->truncate();
            Illuminate\Support\Facades\DB::table('penjualan')->truncate();
            Illuminate\Support\Facades\DB::table('analisa_persediaan')->truncate();
            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            // Reset stok_saat_ini to 0 for all products
            \App\Models\Product::query()->update(['stok_saat_ini' => 0]);
            $logOutput .= "1. Wipe seluruh data transaksi (barang masuk, keluar, stok batch, penjualan, analisa): SUKSES\n";
            $logOutput .= "2. Reset stok semua produk ke 0: SUKSES\n";

            // ─── STEP 2: Reimport penjualan from Gabungan Excel ──────────────────
            $excelFile = $baseDir . '/data produk/Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx';
            if (!file_exists($excelFile)) {
                throw new Exception("File Excel penjualan gabungan tidak ditemukan: " . $excelFile);
            }
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);
            $sheets = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            $janSheet = $spreadsheet->getSheetByName('Januari');
            if (!$janSheet) throw new Exception("Sheet 'Januari' tidak ditemukan!");
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($janSheet->getHighestColumn());
            $productColumns = [];
            for ($colIndex = 4; $colIndex <= $highestColumnIndex; $colIndex++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $productName = trim((string) $janSheet->getCell($colLetter . '3')->getValue());
                if (empty($productName) || in_array(strtolower($productName), ['total qty', 'total penjualan (rp)', 'total penjualan', 'total'])) continue;
                $dbName = $productName;
                $product = \App\Models\Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($dbName)])->first();
                if ($product) $productColumns[$colLetter] = $product->id;
            }
            $insertCount = 0;
            foreach ($sheets as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) { $logOutput .= "Peringatan: Sheet '$sheetName' tidak ditemukan!\n"; continue; }
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $dateVal = $sheet->getCell('B' . $row)->getValue();
                    if (empty($dateVal)) continue;
                    $dateStr = is_numeric($dateVal)
                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal)->format('Y-m-d')
                        : \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                    foreach ($productColumns as $col => $prodId) {
                        $qty = (int) ($sheet->getCell($col . $row)->getValue() ?? 0);
                        if ($qty > 0) {
                            \App\Models\Sale::create([
                                'product_id'        => $prodId,
                                'tanggal_penjualan' => $dateStr,
                                'jumlah_terjual'    => $qty,
                                'sumber_import'     => 'Excel Gabungan',
                                'nama_file_import'  => 'Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx',
                                'user_id'           => 1,
                            ]);
                            $insertCount++;
                        }
                    }
                }
            }
            $logOutput .= "3. Reimport penjualan dari Excel Gabungan: Berhasil ($insertCount baris dimasukkan)\n";

            // ─── STEP 3: Recalculate AU for all products (locked baseline) ───────
            $safetyStockService = app(\App\Services\SafetyStockService::class);
            $recalculatedCount  = 0;
            foreach (array_unique(array_values($productColumns)) as $prodId) {
                $product = \App\Models\Product::find($prodId);
                if ($product) { $safetyStockService->calculate($product); $recalculatedCount++; }
            }
            $logOutput .= "4. Kalkulasi ulang AU / ROP / Safety Stock (baseline terkunci): Berhasil ($recalculatedCount produk)\n";

            $message     = "Reset Bersih Selesai! Semua data uji dihapus, penjualan 31 produk 5 bulan berhasil diimpor ulang, dan AU sudah terkunci ke baseline Jan–Mei 2026.";
            $messageType = "success";
        } elseif ($action === 'import_barang_masuk') {
            // ─── Import Barang Masuk (10 produk, 5 bulan) dari data DOCX ─────────
            // Lokasi dirotasi merata: FRZ-01, FRZ-02, FRZ-03, RAK-A, RAK-B, RAK-C, RAK-D
            $lokasiPool = ['FRZ-01','FRZ-02','FRZ-03','RAK-A','RAK-B','RAK-C','RAK-D'];
            $lokasiIdx  = 0;
            $inserted   = 0;
            $skipped    = 0;

            // Helper: get or create supplier
            $getSupplier = function(string $nama) {
                $s = \App\Models\Supplier::where('nama_supplier', $nama)->first();
                if (!$s) {
                    $s = \App\Models\Supplier::create([
                        'nama_supplier' => $nama,
                        'kontak'        => '-',
                        'alamat'        => '-',
                    ]);
                }
                return $s;
            };

            // Helper: find product (flexible name matching)
            $getProduct = function(string $nama) {
                $map = [
                    'okay sosis 500gr'                  => 'Okey Sosis 500GR',
                    'okey sosis 500gr'                  => 'Okey Sosis 500GR',
                    'fiesta chicken nugget 400 gr'      => 'Nugget Ayam Crispy 400g',
                    'fiesta chicken nugget 400gr'       => 'Nugget Ayam Crispy 400g',
                    'fiesta chicken nugget 450 gr'      => 'Nugget Ayam Crispy 400g',
                    'fiesta chicken nugget 450gr'       => 'Nugget Ayam Crispy 400g',
                    'jamur enoki'                       => 'Jamur Enoki',
                    'meru lapis bogor'                  => 'Meru Lapis Bogor',
                    'okey nugget stik 500gr'            => 'Okey Nugget Stik 500GR',
                    'cireng rujak'                      => 'Cireng Rujak',
                    'salam nugget 500gr'                => 'Salam Nugget 500GR',
                    'warisan isi 50'                    => 'Warisan Isi 50',
                    'belfood sosis isi 30'              => 'Belfood Sosis Isi 30',
                    'richeese nugget'                   => 'Richeese Nugget',
                ];
                $key = strtolower(trim($nama));
                $targetName = $map[$key] ?? null;
                if (!$targetName) return null;
                return \App\Models\Product::whereRaw('LOWER(nama_produk) = ?', [strtolower($targetName)])->first();
            };

            // ── RAW DATA (from DOCX) ─────────────────────────────────────────────
            // Format: [tanggal, nama_produk, jumlah, harga_beli, nama_supplier]
            $rawData = [
                // JANUARI 2026
                ['2026-01-03','Okay Sosis 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-01-14','Okey Sosis 500GR',145,18000,'CV Sony Frozen Food'],
                ['2026-01-27','Okey Sosis 500GR',175,18000,'CV Sony Frozen Food'],
                ['2026-01-06','Fiesta Chicken Nugget 400 gr',185,42000,'PT Champ Citra Mandiri'],
                ['2026-01-21','Fiesta Chicken Nugget 400 gr',215,42000,'PT Champ Citra Mandiri'],
                ['2026-01-05','Jamur Enoki',140,5000,'Hijrafood'],
                ['2026-01-16','Jamur Enoki',110,5000,'Hijrafood'],
                ['2026-01-29','Jamur Enoki',150,5000,'Hijrafood'],
                ['2026-01-08','Meru Lapis Bogor',90,35000,'CV Susan Wilson Reseller'],
                ['2026-01-25','Meru Lapis Bogor',110,35000,'CV Susan Wilson Reseller'],
                ['2026-01-04','Okey Nugget Stik 500GR',170,18000,'CV Sony Frozen Food'],
                ['2026-01-18','Okey Nugget Stik 500GR',160,18000,'CV Sony Frozen Food'],
                ['2026-01-30','Okey Nugget Stik 500GR',170,18000,'CV Sony Frozen Food'],
                ['2026-01-07','Cireng Rujak',175,13000,'CV Roker Jaya Frozen'],
                ['2026-01-24','Cireng Rujak',225,13000,'CV Roker Jaya Frozen'],
                ['2026-01-02','Salam Nugget 500GR',190,18000,'CV Sony Frozen Food'],
                ['2026-01-15','Salam Nugget 500GR',140,18000,'CV Sony Frozen Food'],
                ['2026-01-28','Salam Nugget 500GR',170,18000,'CV Sony Frozen Food'],
                ['2026-01-09','Warisan Isi 50',260,28000,'CV Kylafood Nusantara'],
                ['2026-01-26','Warisan Isi 50',240,28000,'CV Kylafood Nusantara'],
                ['2026-01-10','Belfood Sosis Isi 30',130,18000,'PT Belfoods'],
                ['2026-01-20','Belfood Sosis Isi 30',120,18000,'PT Belfoods'],
                ['2026-01-31','Belfood Sosis Isi 30',150,18000,'PT Belfoods'],
                ['2026-01-05','Richeese Nugget',210,18000,'PT Siomy Makmur'],
                ['2026-01-17','Richeese Nugget',250,18000,'PT Siomy Makmur'],
                ['2026-01-29','Richeese Nugget',240,18000,'PT Siomy Makmur'],
                // FEBRUARI 2026
                ['2026-02-01','Okey Sosis 500GR',120,18000,'CV Sony Frozen Food'],
                ['2026-02-16','Okey Sosis 500GR',100,18000,'CV Sony Frozen Food'],
                ['2026-02-04','Fiesta Chicken Nugget 400 gr',150,42000,'PT Champ Citra Mandiri'],
                ['2026-02-20','Fiesta Chicken Nugget 400 gr',120,42000,'PT Champ Citra Mandiri'],
                ['2026-02-02','Jamur Enoki',90,5000,'Hijrafood'],
                ['2026-02-14','Jamur Enoki',80,5000,'Hijrafood'],
                ['2026-02-26','Jamur Enoki',100,5000,'Hijrafood'],
                ['2026-02-05','Meru Lapis Bogor',70,35000,'CV Susan Wilson Reseller'],
                ['2026-02-22','Meru Lapis Bogor',80,35000,'CV Susan Wilson Reseller'],
                ['2026-02-03','Okey Nugget Stik 500GR',110,18000,'CV Sony Frozen Food'],
                ['2026-02-18','Okey Nugget Stik 500GR',120,18000,'CV Sony Frozen Food'],
                ['2026-02-06','Cireng Rujak',140,13000,'CV Roker Jaya Frozen'],
                ['2026-02-24','Cireng Rujak',120,13000,'CV Roker Jaya Frozen'],
                ['2026-02-01','Salam Nugget 500GR',130,18000,'CV Sony Frozen Food'],
                ['2026-02-17','Salam Nugget 500GR',110,18000,'CV Sony Frozen Food'],
                ['2026-02-08','Warisan Isi 50',180,28000,'CV Kylafood Nusantara'],
                ['2026-02-25','Warisan Isi 50',150,28000,'CV Kylafood Nusantara'],
                ['2026-02-09','Belfood Sosis Isi 30',100,18000,'PT Belfoods'],
                ['2026-02-19','Belfood Sosis Isi 30',90,18000,'PT Belfoods'],
                ['2026-02-27','Belfood Sosis Isi 30',80,18000,'PT Belfoods'],
                ['2026-02-07','Richeese Nugget',180,18000,'PT Siomy Makmur'],
                ['2026-02-19','Richeese Nugget',300,18000,'PT Siomy Makmur'],
                // MARET 2026
                ['2026-03-01','Okey Sosis 500GR',150,18000,'CV Sony Frozen Food'],
                ['2026-03-05','Okey Sosis 500GR',150,18000,'CV Sony Frozen Food'],
                ['2026-03-10','Okey Sosis 500GR',300,18500,'CV Sony Frozen Food'],
                ['2026-03-02','Fiesta Chicken Nugget 450 gr',200,42000,'PT Champ Citra Mandiri'],
                ['2026-03-05','Fiesta Chicken Nugget 450 gr',240,42000,'PT Champ Citra Mandiri'],
                ['2026-03-10','Fiesta Chicken Nugget 450 gr',100,42000,'PT Champ Citra Mandiri'],
                ['2026-03-02','Jamur Enoki',170,5000,'Hijrafood'],
                ['2026-03-05','Jamur Enoki',100,5000,'Hijrafood'],
                ['2026-03-10','Jamur Enoki',300,5000,'Hijrafood'],
                ['2026-03-03','Meru Lapis Bogor',140,35000,'CV Susan Wilson Reseller'],
                ['2026-03-08','Meru Lapis Bogor',210,35000,'CV Susan Wilson Reseller'],
                ['2026-03-14','Meru Lapis Bogor',100,35000,'CV Susan Wilson Reseller'],
                ['2026-03-01','Okey Nugget Stik 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-03-10','Okey Nugget Stik 500GR',300,18000,'CV Sony Frozen Food'],
                ['2026-03-30','Okey Nugget Stik 500GR',250,18000,'CV Sony Frozen Food'],
                ['2026-03-05','Cireng Rujak',180,13000,'CV Roker Jaya Frozen'],
                ['2026-03-13','Cireng Rujak',220,13000,'CV Roker Jaya Frozen'],
                ['2026-03-28','Cireng Rujak',100,13000,'CV Roker Jaya Frozen'],
                ['2026-03-02','Salam Nugget 500GR',190,18000,'CV Sony Frozen Food'],
                ['2026-03-10','Salam Nugget 500GR',300,18000,'CV Sony Frozen Food'],
                ['2026-03-29','Salam Nugget 500GR',260,18000,'CV Sony Frozen Food'],
                ['2026-03-01','Warisan Isi 50',150,28000,'CV Kylafood Nusantara'],
                ['2026-03-06','Warisan Isi 50',170,28000,'CV Kylafood Nusantara'],
                ['2026-03-12','Warisan Isi 50',300,28000,'CV Kylafood Nusantara'],
                ['2026-03-02','Belfood Sosis Isi 30',140,18000,'PT Belfoods'],
                ['2026-03-10','Belfood Sosis Isi 30',300,18000,'PT Belfoods'],
                ['2026-03-29','Belfood Sosis Isi 30',220,18000,'PT Belfoods'],
                ['2026-03-02','Richeese Nugget',170,18000,'PT Siomy Makmur'],
                ['2026-03-09','Richeese Nugget',400,18000,'PT Siomy Makmur'],
                // APRIL 2026
                ['2026-04-02','Okey Sosis 500GR',170,18000,'CV Sony Frozen Food'],
                ['2026-04-16','Okey Sosis 500GR',130,18000,'CV Sony Frozen Food'],
                ['2026-04-28','Okey Sosis 500GR',150,18000,'CV Sony Frozen Food'],
                ['2026-04-04','Fiesta Chicken Nugget 450 gr',170,42000,'PT Champ Citra Mandiri'],
                ['2026-04-22','Fiesta Chicken Nugget 450 gr',180,42000,'PT Champ Citra Mandiri'],
                ['2026-04-03','Jamur Enoki',120,5000,'Hijrafood'],
                ['2026-04-15','Jamur Enoki',100,5000,'Hijrafood'],
                ['2026-04-27','Jamur Enoki',110,5000,'Hijrafood'],
                ['2026-04-07','Meru Lapis Bogor',110,35000,'CV Susan Wilson Reseller'],
                ['2026-04-24','Meru Lapis Bogor',100,35000,'CV Susan Wilson Reseller'],
                ['2026-04-05','Okey Nugget Stik 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-04-19','Okey Nugget Stik 500GR',140,18000,'CV Sony Frozen Food'],
                ['2026-04-30','Okey Nugget Stik 500GR',130,18000,'CV Sony Frozen Food'],
                ['2026-04-06','Cireng Rujak',160,13000,'CV Roker Jaya Frozen'],
                ['2026-04-23','Cireng Rujak',170,13000,'CV Roker Jaya Frozen'],
                ['2026-04-01','Salam Nugget 500GR',170,18000,'CV Sony Frozen Food'],
                ['2026-04-17','Salam Nugget 500GR',150,18000,'CV Sony Frozen Food'],
                ['2026-04-29','Salam Nugget 500GR',130,18000,'CV Sony Frozen Food'],
                ['2026-04-09','Warisan Isi 50',210,28000,'CV Kylafood Nusantara'],
                ['2026-04-25','Warisan Isi 50',190,28000,'CV Kylafood Nusantara'],
                ['2026-04-10','Belfood Sosis Isi 30',120,18000,'PT Belfoods'],
                ['2026-04-18','Belfood Sosis Isi 30',130,18000,'PT Belfoods'],
                ['2026-04-30','Belfood Sosis Isi 30',110,18000,'PT Belfoods'],
                ['2026-04-08','Richeese Nugget',220,18000,'PT Siomy Makmur'],
                ['2026-04-21','Richeese Nugget',180,18000,'PT Siomy Makmur'],
                // MEI 2026
                ['2026-05-02','Okey Sosis 500GR',200,18000,'CV Sony Frozen Food'],
                ['2026-05-08','Okey Sosis 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-05-14','Okey Sosis 500GR',220,18500,'CV Sony Frozen Food'],
                ['2026-05-20','Okey Sosis 500GR',300,18500,'CV Sony Frozen Food'],
                ['2026-05-04','Fiesta Chicken Nugget 450 gr',200,42000,'PT Champ Citra Mandiri'],
                ['2026-05-21','Fiesta Chicken Nugget 450 gr',220,42000,'PT Champ Citra Mandiri'],
                ['2026-05-03','Jamur Enoki',140,5000,'Hijrafood'],
                ['2026-05-12','Jamur Enoki',130,5000,'Hijrafood'],
                ['2026-05-21','Jamur Enoki',250,5000,'Hijrafood'],
                ['2026-05-06','Meru Lapis Bogor',130,35000,'CV Susan Wilson Reseller'],
                ['2026-05-24','Meru Lapis Bogor',150,35000,'CV Susan Wilson Reseller'],
                ['2026-05-05','Okey Nugget Stik 500GR',190,18000,'CV Sony Frozen Food'],
                ['2026-05-18','Okey Nugget Stik 500GR',270,18000,'CV Sony Frozen Food'],
                ['2026-05-30','Okey Nugget Stik 500GR',190,18000,'CV Sony Frozen Food'],
                ['2026-05-03','Cireng Rujak',180,13000,'CV Roker Jaya Frozen'],
                ['2026-05-11','Cireng Rujak',180,13000,'CV Roker Jaya Frozen'],
                ['2026-05-20','Cireng Rujak',200,13000,'CV Roker Jaya Frozen'],
                ['2026-05-01','Salam Nugget 500GR',190,18000,'CV Sony Frozen Food'],
                ['2026-05-11','Salam Nugget 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-05-21','Salam Nugget 500GR',180,18000,'CV Sony Frozen Food'],
                ['2026-05-02','Warisan Isi 50',160,28000,'CV Kylafood Nusantara'],
                ['2026-05-08','Warisan Isi 50',100,28000,'CV Kylafood Nusantara'],
                ['2026-05-14','Warisan Isi 50',250,28000,'CV Kylafood Nusantara'],
                ['2026-05-20','Warisan Isi 50',250,28000,'CV Kylafood Nusantara'],
                ['2026-05-03','Belfood Sosis Isi 30',300,18000,'PT Belfoods'],
                ['2026-05-20','Belfood Sosis Isi 30',300,18900,'PT Belfoods'],
                ['2026-05-31','Belfood Sosis Isi 30',150,18000,'PT Belfoods'],
                ['2026-05-08','Richeese Nugget',250,18000,'PT Siomy Makmur'],
                ['2026-05-19','Richeese Nugget',400,18000,'PT Siomy Makmur'],
                ['2026-05-31','Richeese Nugget',260,18000,'PT Siomy Makmur'],
            ];

            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($rawData as $row) {
                [$tgl, $namaProduk, $jumlah, $hargaBeli, $namaSupplier] = $row;

                $product  = $getProduct($namaProduk);
                if (!$product) { $skipped++; $logOutput .= "SKIP: Produk '$namaProduk' tidak ditemukan di DB\n"; continue; }

                $supplier = $getSupplier($namaSupplier);

                // Assign lokasi merata (rotate)
                $lokasi = $lokasiPool[$lokasiIdx % count($lokasiPool)];
                $lokasiIdx++;

                // Generate batch code
                $dateClean  = str_replace('-', '', $tgl);
                $countToday = \App\Models\IncomingGood::whereDate('tanggal_masuk', $tgl)->count() + 1;
                $batchCode  = 'BM-' . $dateClean . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
                $expiry     = \Carbon\Carbon::parse($tgl)->addMonths(6)->toDateString();

                $incoming = \App\Models\IncomingGood::create([
                    'product_id'           => $product->id,
                    'supplier_id'          => $supplier->id,
                    'tanggal_masuk'        => $tgl,
                    'jumlah'               => $jumlah,
                    'satuan'               => $product->satuan,
                    'harga_beli'           => $hargaBeli,
                    'tanggal_kedaluwarsa'  => $expiry,
                    'batch_code'           => $batchCode,
                    'sumber_import'        => 'Import Demo DOCX',
                    'id_lokasi'            => $lokasi,
                    'keterangan'           => 'Import otomatis dari DATA_BARANG_MASUK_MARET_2026_REVISI.docx',
                    'user_id'              => 1,
                ]);

                \App\Models\StockBatch::create([
                    'product_id'           => $product->id,
                    'incoming_good_id'     => $incoming->id,
                    'batch_code'           => $batchCode,
                    'tanggal_masuk'        => $tgl,
                    'tanggal_kedaluwarsa'  => $expiry,
                    'jumlah_awal'          => $jumlah,
                    'jumlah_sisa'          => $jumlah,
                    'satuan'               => $product->satuan,
                ]);

                $product->increment('stok_saat_ini', $jumlah);
                $inserted++;
            }

            Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $logOutput .= "Import Barang Masuk: $inserted transaksi berhasil diimpor, $skipped dilewati\n";
            $logOutput .= "Lokasi dirotasi merata: FRZ-01, FRZ-02, FRZ-03, RAK-A, RAK-B, RAK-C, RAK-D\n";
            $message     = "Sukses import $inserted data Barang Masuk (10 produk, 5 bulan, Jan–Mei 2026) dari DOCX! Lokasi merata di semua freezer & rak.";
            $messageType = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
        $logOutput .= "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Frozen Mart - Database Manager</title>
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
            --accent-warning: #f59e0b;
            --accent-info: #0284c7;
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
            max-width: 750px;
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
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #38bdf8, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #fbbf24;
        }

        .action-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .action-info {
            flex: 1;
        }

        .action-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 0.25rem;
        }

        .action-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-family: 'Outfit', sans-serif;
        }

        .btn-danger {
            background-color: var(--accent-danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.4);
        }

        .btn-info {
            background-color: var(--accent-info);
            color: white;
        }

        .btn-info:hover {
            background-color: #026ca3;
            box-shadow: 0 0 12px rgba(2, 132, 199, 0.4);
        }

        .btn-secondary {
            background-color: #475569;
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #334155;
        }

        .log-section {
            margin-top: 1.5rem;
        }

        .log-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .log-box {
            background: #090d16;
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85rem;
            border: 1px solid rgba(255,255,255,0.05);
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
            color: #a7f3d0;
        }

        footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Pengatur Database Demo</h1>
            <p>Sistem Informasi Persediaan Della Frozen Mart</p>
        </header>

        <?php if (!$vendorExists): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> Folder <code>vendor/</code> tidak ditemukan. Harap unggah folder vendor lengkap agar Laravel dapat berjalan.
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning">
            ⚠️ <strong>Perhatian:</strong> Halaman ini digunakan khusus untuk bimbingan/demo dosen. Memilih tindakan di bawah ini akan menghapus data di database saat ini. Pastikan Anda memahami konsekuensinya.
        </div>

        <?php if ($vendorExists): ?>
            <!-- Opsi 1: Bersihkan Data Produk & Transaksi (Mode Demo Kosong) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">1. Reset ke Mode Kosong (Hanya Akun Login)</h3>
                    <p class="action-desc">Mengosongkan semua data produk, kategori, supplier, dan transaksi. Hanya menyisakan 3 akun login default (Admin, Manager, Owner) agar Anda bisa mendemokan penginputan dari awal.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin MENGOSONGKAN semua data produk dan transaksi? Tindakan ini tidak bisa dibatalkan.');">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger">Kosongkan Database</button>
                </form>
            </div>

            <!-- Opsi 2: Isi Kembali Data Contoh (Full Seed) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">2. Isi Kembali Data Contoh (Restore Mock Data)</h3>
                    <p class="action-desc">Membuat ulang semua tabel dan mengisinya kembali dengan data simulasi bawaan (produk, batch, penjualan, analisis ROP) secara otomatis.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan seluruh data simulasi contoh?');">
                    <input type="hidden" name="action" value="seed">
                    <button type="submit" class="btn btn-info">Isi Data Contoh</button>
                </form>
            </div>

            <!-- Opsi 3: Reset Semua Stok Produk ke 0 -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">3. Reset Semua Stok Produk Menjadi 0</h3>
                    <p class="action-desc">Mengosongkan seluruh transaksi barang masuk, barang keluar, batch stok, opname, penjualan, dan menyetel stok semua produk menjadi 0. Menjaga data master produk, kategori, dan supplier tetap aman.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin MERESET SEMUA STOK produk menjadi 0? Seluruh riwayat transaksi akan dihapus.');">
                    <input type="hidden" name="action" value="reset_stock">
                    <button type="submit" class="btn btn-danger" style="background-color: var(--accent-warning); box-shadow: none;">Reset Stok ke 0</button>
                </form>
            </div>

            <!-- Opsi 4: Impor Data Penjualan dari Excel (10 Produk) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">4. Impor Data Penjualan 5 Bulan (10 Produk Utama)</h3>
                    <p class="action-desc">Membaca file <code>Della_FrozenMart_Jan-Mei_2026_Final.xlsx</code>, mengimpor seluruh riwayat transaksi penjualan selama 5 bulan untuk 10 produk utama, dan menghitung otomatis ROP serta Safety Stock-nya.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data penjualan 5 bulan dari file Excel?');">
                    <input type="hidden" name="action" value="import_sales">
                    <button type="submit" class="btn btn-info" style="background-color: var(--accent-success); box-shadow: none;">Impor Data Penjualan</button>
                </form>
            </div>

            <!-- Opsi 5: Impor Data Penjualan Sisa dari Excel (22 Produk) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">5. Impor Data Penjualan Sisa 5 Bulan (22 Produk Lainnya)</h3>
                    <p class="action-desc">Membaca file <code>Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx</code>, mengimpor transaksi penjualan selama 5 bulan untuk 22 produk sisa (melewati 2 produk non-resmi secara otomatis), dan menghitung otomatis ROP serta Safety Stock.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data penjualan sisa 5 bulan untuk 22 produk?');">
                    <input type="hidden" name="action" value="import_remaining_sales">
                    <button type="submit" class="btn btn-info" style="background-color: var(--accent-info); box-shadow: none;">Impor Penjualan Sisa</button>
                </form>
            </div>

            <!-- Opsi 6: Impor Data Barang Masuk dari Excel (22 Produk) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">6. Impor Data Barang Masuk 5 Bulan (22 Produk)</h3>
                    <p class="action-desc">Membaca file <code>DATA_SIMULASI_BARANG_MASUK_JAN-MEI_2026 (1).xlsx</code>, mengimpor seluruh transaksi barang masuk (5 bulan) untuk 22 produk resmi ke lokasi Rak A/B/C/D dan Freezer 1/2/3 secara otomatis, mengupdate stok, dan menghitung otomatis ROP/Safety Stock.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data barang masuk 5 bulan untuk 22 produk?');">
                    <input type="hidden" name="action" value="import_incoming_goods">
                    <button type="submit" class="btn btn-info" style="background-color: var(--accent-success); box-shadow: none;">Impor Barang Masuk</button>
                </form>
            </div>

            <!-- Opsi 7: Impor Data Barang Keluar Sisa dari Excel (22 Produk) -->
            <div class="action-card">
                <div class="action-info">
                    <h3 class="action-title">7. Impor Data Barang Keluar Sisa 5 Bulan (22 Produk)</h3>
                    <p class="action-desc">Membaca file <code>Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx</code>, mengimpor seluruh transaksi penjualan sebagai barang keluar (5 bulan) untuk 22 produk resmi, memotong stok fisik dan batch secara otomatis dengan metode FIFO (mendukung stok negatif jika terjadi defisit).</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data barang keluar sisa 5 bulan untuk 22 produk?');">
                    <input type="hidden" name="action" value="import_remaining_outgoing">
                    <button type="submit" class="btn btn-info" style="background-color: var(--accent-info); box-shadow: none;">Impor Barang Keluar Sisa</button>
                </form>
            </div>

            <!-- Opsi 8: Impor Data Penjualan Gabungan 31 Produk dari Excel -->
            <div class="action-card" style="border: 2px solid rgba(56, 189, 248, 0.4); background-color: rgba(56, 189, 248, 0.05);">
                <div class="action-info">
                    <h3 class="action-title" style="color: #38bdf8;">8. Impor Data Penjualan Gabungan 5 Bulan (31 Produk)</h3>
                    <p class="action-desc">Membaca file gabungan terbaru <code>data produk/Della_FrozenMart_31Produk_Jan-Mei_2026_Gabungan (1).xlsx</code>, mengosongkan seluruh riwayat penjualan lama, lalu mengimpor seluruh data penjualan 31 produk sekaligus untuk sinkronisasi sempurna.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor seluruh data penjualan gabungan 31 produk? Seluruh data penjualan lama akan di-reset.');">
                    <input type="hidden" name="action" value="import_combined_sales">
                    <button type="submit" class="btn btn-info" style="background-color: #0284c7; box-shadow: none;">Impor Penjualan Gabungan</button>
                </form>
            </div>

            <!-- Opsi 9: Full Reset Demo – Bersihkan Semua Data Uji + Reimport -->
            <div class="action-card" style="border: 2px solid rgba(239, 68, 68, 0.5); background-color: rgba(239, 68, 68, 0.06);">
                <div class="action-info">
                    <h3 class="action-title" style="color: #f87171;">9. ⚠️ Reset Bersih + Reimport Lengkap (31 Produk)</h3>
                    <p class="action-desc">
                        <strong style="color:#fca5a5;">HAPUS SEMUA data transaksi</strong> (barang masuk, barang keluar, stok batch, penjualan, analisa) lalu reimport otomatis seluruh data penjualan 31 produk 5 bulan dari Excel Gabungan.
                        AU dikalkulasi ulang menggunakan baseline terkunci (Jan–Mei 2026).
                        Gunakan ini untuk membersihkan data uji dan kembali ke kondisi demo yang bersih.
                    </p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('⚠️ PERINGATAN: Seluruh data barang masuk, barang keluar, stok, dan penjualan akan DIHAPUS dan diimpor ulang dari Excel. Lanjutkan?');">
                    <input type="hidden" name="action" value="full_reset_demo">
                    <button type="submit" class="btn btn-danger" style="background-color: #dc2626; box-shadow: none;">Reset Bersih &amp; Reimport</button>
                </form>
            </div>

            <!-- Opsi 10: Impor Data Barang Masuk dari DOCX (10 Produk) -->
            <div class="action-card" style="border: 2px solid rgba(16, 185, 129, 0.4); background-color: rgba(16, 185, 129, 0.05);">
                <div class="action-info">
                    <h3 class="action-title" style="color: #10b981;">10. Impor Data Barang Masuk 5 Bulan (10 Produk Utama - DOCX)</h3>
                    <p class="action-desc">Mengimpor seluruh riwayat barang masuk dari data <code>DATA_BARANG_MASUK_MARET_2026_REVISI.docx</code> selama 5 bulan untuk 10 produk utama. Lokasi penyimpanan akan didistribusikan secara seimbang/merata ke <code>FRZ-01, FRZ-02, FRZ-03, RAK-A, RAK-B, RAK-C, RAK-D</code> agar data laporan rapi dan seimbang.</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data barang masuk 5 bulan dari data DOCX?');">
                    <input type="hidden" name="action" value="import_barang_masuk">
                    <button type="submit" class="btn btn-success" style="background-color: #059669; box-shadow: none;">Impor Barang Masuk DOCX</button>
                </form>
            </div>
        <?php endif; ?>



        <?php if (!empty($logOutput)): ?>
            <div class="log-section">
                <h4 class="log-title">Log Proses Eksekusi:</h4>
                <div class="log-box"><?php echo htmlspecialchars($logOutput); ?></div>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="/" class="btn btn-secondary">Kembali ke Dashboard Utama</a>
        </div>

        <footer>
            <p>Database Demo Manager &copy; 2026. Antigravity AI Coding Assistant.</p>
        </footer>
    </div>
</body>
</html>
