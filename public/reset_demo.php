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
        // Bootstrap Laravel
        require $baseDir . '/vendor/autoload.php';
        $app = require_once $baseDir . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
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
                'D' => 'Sosis okay 500g',         // Excel col D: Okey Sosis 500GR  → PRD-0011
                'E' => 'Fiesta chicken nugget 450gr',    // Excel col E: Fiesta Nugget 450gr → PRD-0028
                'F' => 'jamur enoki',             // Excel col F: Jamur Enoki         → PRD-0012
                'G' => 'Meru Lapis Bogor',        // Excel col G: Meru Lapis Bogor    → PRD-0022
                // 'H' => SKIP (Okey Nugget Stik - tidak ada di 31 produk resmi)
                'I' => 'Cireng Rujak 15gr',       // Excel col I: Cireng Rujak         → PRD-0008
                'J' => 'Sallam Nugget 500 gr',   // Excel col J: Sallam Nugget 500gr  → PRD-0026
                'K' => 'WARISAN ISI 50',          // Excel col K: Warisan Isi 50        → PRD-0018
                'L' => 'Belfood Sosis Isi 30',   // Excel col L: Belfood Sosis Isi 30 → PRD-0030
                'M' => 'Richees Nugget',          // Excel col M: Richeese Nugget      → PRD-0023
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
                
                // Naming mapping logic (handling DB typos/casing)
                $dbName = $productName;
                if ($productName === 'Champ Nugget Kombinasi 450GR') {
                    $dbName = 'Champ Nugget KombinasiI 450GR'; // database typo with double I
                }
                
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
            
            // Delete previously imported outgoing goods from this file to prevent duplicates (make it idempotent)
            Illuminate\Support\Facades\DB::table('barang_keluar')
                ->where('keterangan', 'Dari import barang keluar sisa')
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
            
            // Build products cache maps
            $products = \App\Models\Product::all();
            $dbProductsCache = [];
            foreach ($products as $p) {
                $dbProductsCache[strtolower(trim($p->nama_produk))] = $p->id;
            }
            // Add mapping for typo/spelling
            if (isset($dbProductsCache[strtolower('Champ Nugget KombinasiI 450GR')])) {
                $dbProductsCache[strtolower('Champ Nugget Kombinasi 450GR')] = $dbProductsCache[strtolower('Champ Nugget KombinasiI 450GR')];
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
                            \App\Models\OutgoingGood::create([
                                'product_id' => $productsCache[$col],
                                'tanggal_keluar' => $dateStr,
                                'jumlah' => $qty,
                                'jenis_keluar' => 'penjualan',
                                'keterangan' => 'Dari import barang keluar sisa',
                                'user_id' => $userId,
                            ]);
                            $insertCount++;
                        }
                    }
                }
            }
            
            $logOutput .= "1. Impor data barang keluar dari Excel: Berhasil ($insertCount baris dimasukkan)\n";
            $message = "Sukses mengimpor data barang keluar sisa 5 bulan untuk 22 produk tambahan! Data masuk ke tabel barang keluar tanpa memotong stok fisik.";
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
                    <p class="action-desc">Membaca file <code>Della_FrozenMart_24Produk_Tambahan_Jan-Mei_2026.xlsx</code>, mengimpor seluruh transaksi penjualan sebagai riwayat barang keluar (5 bulan) untuk 22 produk tambahan tanpa memotong stok fisik (agar stok tetap aman dan positif).</p>
                </div>
                <form method="POST" action="?key=<?php echo htmlspecialchars($secureKey); ?>" onsubmit="return confirm('Apakah Anda yakin ingin mengimpor data barang keluar sisa 5 bulan untuk 22 produk?');">
                    <input type="hidden" name="action" value="import_remaining_outgoing">
                    <button type="submit" class="btn btn-info" style="background-color: var(--accent-info); box-shadow: none;">Impor Barang Keluar Sisa</button>
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
