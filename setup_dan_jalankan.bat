@echo off
setlocal enabledelayedexpansion
set "choice="
cd /d "%~dp0"
title Setup ^& Launcher - Della Frozen Mart
color 0F
cls

echo =======================================================================
echo          DELLA FROZEN MART - BATCH SETUP ^& LAUNCHER (1-CLICK)
echo =======================================================================
echo.
echo Script ini dirancang untuk menyiapkan dan menjalankan aplikasi
echo Della Frozen Mart di laptop baru/teman Anda dengan mudah.
echo.
echo =======================================================================
echo.

:: 1. DETEKSI PATH PHP XAMPP
echo [1/6] Memeriksa instalasi PHP...
where php >nul 2>nul
if %errorlevel% equ 0 goto php_found
echo - PHP tidak terdaftar di PATH global.
echo - Memeriksa lokasi default XAMPP (C:\xampp\php)...
if exist "C:\xampp\php\php.exe" (
    set "PATH=%PATH%;C:\xampp\php"
    echo - BERHASIL: PHP XAMPP ditambahkan sementara ke sesi terminal ini.
    goto php_found
)
echo.
echo [ERROR] PHP tidak ditemukan!
echo Aplikasi ini membutuhkan PHP versi 8.2 atau 8.3 - bawaan XAMPP baru.
echo Silakan instal XAMPP di lokasi standar (C:\xampp) terlebih dahulu.
echo.
pause
exit /b

:php_found
echo - BERHASIL: PHP terdeteksi di sistem Anda.
php -v | findstr /i "php"
echo.

:: MEMERIKSA EKSTENSI PHP YANG DIBUTUHKAN
echo Memeriksa ekstensi PHP...
php -d display_errors=0 -d error_reporting=0 -r "$req=['gd','zip','fileinfo','intl','pdo_mysql'];$missing=[];foreach($req as $e){if(!extension_loaded($e)){$missing[]=$e;}}if(!empty($missing)){echo 'MISSING:'.implode(',',$missing);}else{echo 'OK';}" > temp_ext_check.txt 2>nul
set /p ext_status=<temp_ext_check.txt
del temp_ext_check.txt

if not "%ext_status:~0,7%"=="MISSING" goto ext_ok
set "missing_list=%ext_status:MISSING:=%"
echo.
echo [PERINGATAN] Beberapa ekstensi PHP penting belum aktif di php.ini:
echo Kebutuhan yang belum aktif: !missing_list!
echo.
echo Cara mengaktifkannya di XAMPP:
echo 1. Buka XAMPP Control Panel.
echo 2. Klik 'Config' pada Apache -^ Pilih 'PHP php.ini'.
echo 3. Cari baris ';extension=nama_ekstensi' - contoh: ;extension=zip.
echo 4. Hapus tanda titik koma (;) di depannya agar menjadi 'extension=zip'.
echo 5. Simpan file dan restart Apache Anda.
echo.
echo Aplikasi akan tetap dicoba untuk dijalankan, namun fitur Excel/PDF mungkin eror.
echo.
pause
goto ext_end

:ext_ok
echo - BERHASIL: Semua ekstensi PHP yang diperlukan (GD, ZIP, Intl, Fileinfo) telah aktif.

:ext_end
echo.

:: 2. DETEKSI / INSTAL COMPOSER
echo [2/6] Memeriksa Composer (PHP Package Manager)...
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo - Composer tidak terdaftar di PATH global.
    if exist "composer.phar" (
        echo - BERHASIL: Menemukan file composer.phar lokal.
        set "COMPOSER_CMD=php composer.phar"
    ) else (
        echo - Mengunduh composer.phar secara otomatis...
        curl -sS -o composer.phar https://getcomposer.org/composer.phar >nul 2>&1
        if exist "composer.phar" (
            echo - BERHASIL: composer.phar berhasil diunduh secara lokal.
            set "COMPOSER_CMD=php composer.phar"
        ) else (
            echo.
            echo [PERINGATAN] Gagal mengunduh Composer secara otomatis.
            echo Pastikan laptop terhubung ke internet.
            echo Silakan unduh dan pasang Composer secara manual dari: https://getcomposer.org/
            echo.
            pause
            exit /b
        )
    )
) else (
    echo - BERHASIL: Composer global terdeteksi.
    set "COMPOSER_CMD=composer"
)
echo.

:: 3. PERSIAPAN FILE .ENV
echo [3/6] Menyiapkan file konfigurasi (.env)...
if not exist ".env" (
    echo - File .env tidak ditemukan. Menyalin dari .env.example...
    copy .env.example .env >nul
    if exist ".env" (
        echo - BERHASIL: File .env telah dibuat.
    ) else (
        echo [ERROR] Gagal membuat file .env.
        pause
        exit /b
    )
) else (
    echo - BERHASIL: File .env sudah tersedia.
)
echo.

:: 4. MEMERIKSA KONEKSI DATABASE (MYSQL)
echo [4/6] Memeriksa koneksi database MySQL...
:check_mysql
php -r "$c=@new mysqli('127.0.0.1','root',''); if($c->connect_error){ exit(1); } exit(0);" >nul 2>nul
if %errorlevel% equ 0 goto mysql_ok
echo.
echo =======================================================================
echo [PENTING] MySQL di XAMPP belum aktif/running - Koneksi Ditolak
echo =======================================================================
echo.
echo Beberapa penyebab umum MySQL XAMPP tidak bisa start:
echo A. Port 3306 sedang digunakan oleh MySQL Server lain - bukan XAMPP.
echo B. Berkas log transaksi di database korup/rusak setelah PC mati mendadak.
echo.
echo Pilihlah langkah perbaikan di bawah ini:
echo -----------------------------------------------------------------------
echo [1] Jalankan proyek - pastikan XAMPP sudah dinyalakan
echo [2] Perbaiki MySQL XAMPP Crash - Hapus log korup otomatis - AMAN/NO DATA LOSS
echo [3] Cek konflik port 3306 - Deteksi dan matikan aplikasi lain yang memakai 3306
echo [4] Cek kembali koneksi database secara manual
echo [5] Keluar
echo -----------------------------------------------------------------------
set "choice="
set /p "choice=Masukkan pilihan (1-5): "

if "!choice!"=="1" (
    echo Memastikan XAMPP sudah dinyalakan...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
        timeout /t 3 >nul
    )
    goto check_mysql
)
if "!choice!"=="2" (
    call :fix_mysql_logs
    goto check_mysql
)
if "!choice!"=="3" (
    call :check_port_conflict
    goto check_mysql
)
if "!choice!"=="4" (
    goto check_mysql
)
if "!choice!"=="5" (
    exit /b
)
goto check_mysql

:mysql_ok
echo - BERHASIL: Terhubung ke MySQL.

:: MEMBUAT DATABASE BILA BELUM ADA
php -r "$c=new mysqli('127.0.0.1','root',''); $c->query('CREATE DATABASE IF NOT EXISTS della_frozenmart');"
echo - BERHASIL: Database 'della_frozenmart' siap digunakan.
echo.

:: 5. INSTAL DEPENDENSI & RUN MIGRATIONS
echo [5/6] Menginstal library/dependensi Laravel (Composer)...
echo Ini memerlukan koneksi internet untuk unduhan pertama kali. Mohon tunggu...
call %COMPOSER_CMD% install --no-interaction
if %errorlevel% neq 0 (
    echo.
    echo [PERINGATAN] Gagal menginstal library lewat Composer.
    echo Pastikan Anda terkoneksi ke internet dan coba jalankan 'composer install' manual.
    pause
) else (
    echo - BERHASIL: Library terinstal dengan baik.
)

:: GENERATE KEY JIKA BELUM ADA
php artisan key:generate --no-interaction
echo - BERHASIL: Application Key siap.

:: RUN MIGRATIONS & SEEDER
echo.
echo Menyiapkan struktur tabel database dan data contoh...
php artisan migrate --seed --force
if %errorlevel% neq 0 (
    echo [PERINGATAN] Terjadi masalah saat migrasi database.
    echo Coba periksa konfigurasi database Anda di file .env.
    pause
) else (
    echo - BERHASIL: Tabel dan data contoh siap di database.
)
echo.

:: 6. JALANKAN APLIKASI
echo [6/6] Menjalankan server lokal web Della Frozen Mart...
echo.
echo =======================================================================
echo APLIKASI TELAH SIAP DIJALANKAN!
echo.
echo Server lokal akan diaktifkan di background.
echo Anda dapat mengakses aplikasi di browser melalui link berikut:
echo 👉 http://127.0.0.1:8000
echo.
echo Gunakan akun login default berikut:
echo - Admin: admin@della.test (password: password)
echo - Manager: manager@della.test (password: password)
echo - Owner: owner@della.test (password: password)
echo =======================================================================
echo.
echo Membuka browser secara otomatis...
start http://127.0.0.1:8000
php artisan serve
pause
exit /b


:: =======================================================================
:: SUBROUTINE: PERBAIKI LOG MYSQL CRASH
:: =======================================================================
:fix_mysql_logs
echo.
echo Memeriksa log mysql di C:\xampp\mysql\data...
:: Matikan mysqld yang mungkin menggantung
taskkill /F /IM mysqld.exe >nul 2>&1
set "mysql_data=C:\xampp\mysql\data"
if exist "%mysql_data%" (
    echo Menghapus berkas log korup lama agar MySQL dapat start bersih...
    if exist "%mysql_data%\aria_log_control" (
        echo - Menghapus aria_log_control...
        del /F /Q "%mysql_data%\aria_log_control"
    )
    if exist "%mysql_data%\ib_logfile0" (
        echo - Menghapus ib_logfile0...
        del /F /Q "%mysql_data%\ib_logfile0"
    )
    if exist "%mysql_data%\ib_logfile1" (
        echo - Menghapus ib_logfile1...
        del /F /Q "%mysql_data%\ib_logfile1"
    )
    if exist "%mysql_data%\*.pid" (
        echo - Menghapus berkas .pid lama...
        del /F /Q "%mysql_data%\*.pid"
    )
    echo - BERHASIL: Berkas log korup dan .pid lama berhasil dibersihkan.
    echo Silakan coba pilih Menu [1] kembali untuk menjalankan MySQL.
) else (
    echo [ERROR] Direktori data MySQL di %mysql_data% tidak ditemukan.
)
echo.
pause
exit /b


:: =======================================================================
:: SUBROUTINE: CEK KONFLIK PORT 3306
:: =======================================================================
:check_port_conflict
echo.
echo Memeriksa port 3306...
set "conflicting_pid="
for /f "tokens=5" %%a in ('netstat -aon ^| findstr /r /c:":3306 "') do (
    set "conflicting_pid=%%a"
)

if defined conflicting_pid (
    echo [PERINGATAN] Port 3306 sedang dipakai oleh PID: !conflicting_pid!
    echo Ini disebabkan karena ada service MySQL Server mandiri yang berjalan di PC ini.
    echo.
    set /p "kill_port=Apakah Anda ingin mematikan paksa PID !conflicting_pid! agar port 3306 terbebas? (Y/N): "
    if /i "!kill_port!"=="Y" (
        taskkill /F /PID !conflicting_pid!
        if !errorlevel! equ 0 (
            echo [BERHASIL] Proses !conflicting_pid! telah dimatikan. Port 3306 bebas.
        ) else (
            echo [GAGAL] Tidak dapat mematikan proses. Silakan buka 'Task Manager'
            echo pilih 'Details', cari PID !conflicting_pid! atau service mysql, lalu End Task.
        )
    )
) else (
    echo - Port 3306 bersih, tidak ada konflik port.
)
echo.
pause
exit /b
