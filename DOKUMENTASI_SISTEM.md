# 📘 DOKUMENTASI LENGKAP SISTEM INFORMASI PERSEDIAAN DELLA FROZEN MART

Dokumen ini menyajikan penjelasan lengkap, detail teknis, arsitektur data, mekanisme keamanan (RBAC), serta alur bisnis dari **Sistem Informasi Persediaan Della Frozen Mart**.

---

## 1. PENDAHULUAN & SPESIFIKASI TEKNOLOGI

Sistem Informasi Persediaan Della Frozen Mart dirancang khusus untuk mengelola operasional persediaan toko makanan beku (frozen food), dengan fokus utama pada **optimalisasi tingkat stok** guna menghindari penumpukan barang (overstock) maupun kehabisan barang (stockout). Sistem ini mengintegrasikan metode analisis persediaan ilmiah (**Safety Stock & Reorder Point**) dengan pelacakan fisik barang menggunakan metode pencatatan arus biaya **FEFO (First Expired, First Out) / FIFO (First In, First Out)**.

### Stack Teknologi Utama
*   **Backend Framework**: Laravel (PHP 8.2 / 8.3)
*   **Database**: MySQL
*   **Frontend**: Blade Templating Engine, CSS Vanilla (mengacu pada [DESIGN.md](file:///d:/ANTIGRAVITY/della-frozenmart/DESIGN.md)), Vanilla JavaScript.
*   **Aksen Visual / Gaya**: *Soft Glassmorphism* (kartu semi-transparan, gradasi latar belakang dinamis, transisi halus, tata letak modern).
*   **Library Eksternal**:
    *   **Maatwebsite/Laravel-Excel**: Digunakan untuk impor data bulk (penjualan & faktur pembelian) dan ekspor laporan ke format Excel (CSV).
    *   **Chart.js / ApexCharts**: Representasi visual untuk tren omzet penjualan bulanan dan status persediaan (Pie Chart).
    *   **Phosphor Icons / Heroicons**: Kumpulan ikon antarmuka yang bersih dan konsisten.

---

## 2. ARSITEKTUR DATABASE & STRUKTUR TABEL

Sistem ini didukung oleh **15 tabel database** yang saling berelasi erat untuk memastikan konsistensi data operasional dan riwayat audit. Detail diagram kelas dan dependensi logika bisnis selengkapnya dapat dilihat pada berkas **[CLASS_DIAGRAM.md](file:///d:/ANTIGRAVITY/della-frozenmart/CLASS_DIAGRAM.md)**.

Berikut adalah rincian fungsionalitas dari setiap tabel:

1.  **`users`**: Menyimpan kredensial pengguna, nama, status keaktifan (`status_aktif`), serta peran operasional (`role`): `admin`, `manager`, atau `owner`.
2.  **`categories`** (`kategori`): Klasifikasi kelompok produk (misalnya: Sosis, Nugget, Bakso, Kentang).
3.  **`products`** (`produk`): Informasi dasar produk yang meliputi kode produk unik (`PRD-XXXX`), nama produk, batas stok minimum, stok saat ini (`stok_saat_ini`), satuan penjualan (pack, pcs, kg), dan tanggal kedaluwarsa terdekat.
4.  **`suppliers`** (`supplier`): Daftar supplier penyedia barang beserta alamat dan kontak.
5.  **`incoming_goods`** (`barang_masuk`): Log transaksi barang masuk yang mencatat supplier asal, tanggal masuk, harga beli per unit (`harga_beli`), jumlah masuk, nomor batch unik, dan lokasi penyimpanan.
6.  **`stock_batches`** (`batch_stok`): Tabel krusial untuk pelacakan FIFO-FEFO. Mencatat histori masuk per batch, jumlah awal (`jumlah_awal`), sisa kuantitas saat ini (`jumlah_sisa`), dan tanggal kedaluwarsa spesifik batch tersebut.
7.  **`outgoing_goods`** (`barang_keluar`): Log transaksi barang keluar yang mengklasifikasikan jenis pengeluaran seperti `penjualan`, `rusak`, `kedaluwarsa`, atau `penyesuaian`.
8.  **`outgoing_good_details`** (`detail_barang_keluar`): Tabel pivot penghubung antara `barang_keluar` dan `batch_stok`. Mencatat batch mana saja yang terpotong beserta jumlah kuantitas yang diambil dari batch tersebut (sesuai aturan FIFO/FEFO).
9.  **`sales`** (`penjualan`): Riwayat data penjualan volume produk yang diimpor dari data kasir atau dicatat sistem. Digunakan sebagai basis data kalkulasi Safety Stock & ROP.
10. **`stock_opnames`** (`stok_opname`): Audit persediaan fisik secara periodik. Mencatat selisih (`selisih`) antara stok sistem (`stok_sistem`) dengan jumlah riil di lapangan (`stok_fisik`).
11. **`inventory_analyses`** (`analisa_persediaan`): Menyimpan kalkulasi Safety Stock, Reorder Point (ROP), rata-rata penggunaan harian, penjualan maksimal harian, status stok (`Aman`, `Warning`, `Order`), serta teks rekomendasi otomatis.
12. **`purchase_orders`** (`pemesanan_supplier`): Modul pelacakan pesanan barang ke supplier. Menyimpan status pesanan (`draft`, `sent`, `received`, `cancelled`) dan dapat dikonversi langsung menjadi transaksi barang masuk setelah barang diterima fisik.
13. **`notifications`** (`notifikasi`): Peringatan otomatis mengenai stok menipis, barang mendekati kedaluwarsa, sisa batch kosong, atau status impor berkas.
14. **`import_logs`** (`log_import`): Catatan riwayat unggah berkas (nama berkas, total baris, jumlah baris sukses, jumlah baris gagal, dan pesan error JSON) guna transparansi audit data.
15. **`activity_logs`** (`log_aktivitas`): Rekaman jejak audit (*audit trail*) setiap aktivitas pengguna (misalnya pemicuan analisis, tambah barang, ubah data) lengkap dengan alamat IP dan user agent.

---

## 3. KEAMANAN AKSES (ROLE-BASED ACCESS CONTROL - RBAC)

Sistem mengadopsi prinsip keamanan berlapis (Double-Gated Security) yang membatasi akses pengguna baik secara visual di antarmuka (Blade Views) maupun secara fungsional di server (Route Middleware).

Berikut detail matriks wewenang dan batasan untuk masing-masing peran:

### A. Admin (Staff Gudang / Operasional Master Data)
Admin fokus pada pencatatan fisik barang di lapangan dan validasi data produk/supplier.
*   **Hak Akses (Bisa diakses)**:
    *   Kelola Master Data (CRUD): Produk, Kategori, Supplier, dan Pengguna.
    *   Mencatat transaksi Barang Masuk, Barang Keluar, dan Stok Opname.
    *   Memicu kalkulasi Safety Stock & ROP per produk secara manual.
    *   Menerima Notifikasi Peringatan Stok Kritis (Warning/Order/Expired).
    *   Melihat Laporan Barang Masuk & Laporan Barang Keluar FIFO COGS (dan ekspor Excel/PDF).
    *   Mengakses Laporan Penjualan Produk (tanpa data finansial).
*   **Batasan Keamanan (Dilarang)**:
    *   Tidak dapat mengimpor berkas Penjualan (Hanya untuk Manager).
    *   Tidak dapat melihat visualisasi Pie Chart Status Stok Owner di dashboard.
    *   **Keamanan Finansial**: Angka keuangan (Harga Jual, Total Omzet, Laba Kotor, Margin) disembunyikan secara dinamis pada web, cetak PDF, dan ekspor Excel.

### B. Manager (Supervisor / Analis Perencanaan)
Manager fokus pada pengawasan pasokan, pengelolaan impor data, dan analisis perencanaan pemesanan persediaan.
*   **Hak Akses (Bisa diakses)**:
    *   Mengunggah spreadsheet penjualan harian/bulanan (Menu khusus "Import Penjualan").
    *   Mencatat transaksi Barang Masuk, Barang Keluar, dan Stok Opname.
    *   Memicu kalkulasi Safety Stock & ROP.
    *   Melihat Laporan Barang Masuk & Laporan Penjualan Produk (tanpa data finansial).
*   **Batasan Keamanan (Dilarang)**:
    *   Diblokir dari Master Data CRUD (Produk/Supplier) guna menjaga orisinalitas data referensi.
    *   Diblokir dari Laporan Barang Keluar (FIFO COGS).
    *   Tidak memiliki akses ke visualisasi Status Stok (Pie Chart Owner).
    *   **Keamanan Finansial**: Seluruh metrik keuangan (Harga Jual, Omzet, Laba, Margin) di dashboard maupun laporan disembunyikan secara dinamis.

### C. Owner (Pemilik Toko / Manajemen Finansial)
Owner bertindak sebagai pemantau utama kesehatan bisnis, profitabilitas, serta arus keuangan perusahaan.
*   **Hak Akses (Bisa diakses)**:
    *   Melihat dashboard keuangan lengkap (Total Omzet, Laba Kotor, Tren Omzet bulanan, Produk Terlaris).
    *   Mengakses menu visualisasi Status Stok komprehensif (Pie Chart: Aman vs Warning vs Order).
    *   Melihat Laporan Barang Masuk dan Barang Keluar FIFO lengkap.
    *   Mengakses Laporan Penjualan Lengkap (termasuk seluruh nilai finansial sensitif).
    *   Mengunduh ekspor Excel & cetak PDF yang menampilkan data finansial penuh.
*   **Batasan Keamanan (Dilarang)**:
    *   Diblokir dari transaksi operasional (tidak bisa input barang masuk/keluar, stok opname, atau impor excel) untuk memenuhi prinsip **Separation of Duties** (Pemisahan Tugas).
    *   Diblokir dari modifikasi master data produk/supplier.
    *   Diblokir dari menu pemicuan kalkulasi Safety Stock utama secara manual.

---

## 4. ALUR BISNIS & ALGORITMA UTAMA

### A. Algoritma Pengurangan Stok FIFO-FEFO (`FifoService.php`)
Ketika terjadi transaksi Barang Keluar (baik karena penjualan, barang rusak, maupun kedaluwarsa), sistem tidak langsung memotong total stok produk secara acak, melainkan menggunakan algoritma kombinasi **FEFO (First Expired, First Out)** dan **FIFO (First In, First Out)** pada tingkat batch:
1.  Sistem mencari batch stok (`stock_batches`) yang terkait dengan produk tersebut dan memiliki sisa kuantitas (`jumlah_sisa`) > 0.
2.  Batch-batch tersebut diurutkan berdasarkan prioritas:
    *   **Tanggal Kedaluwarsa Terdekat** (FEFO - produk dengan tanggal kedaluwarsa paling cepat akan dikeluarkan terlebih dahulu. Batch tanpa tanggal kedaluwarsa diletakkan paling akhir).
    *   **Tanggal Masuk Terlama** (FIFO - jika tanggal kedaluwarsa sama atau bernilai null, sistem memprioritaskan batch yang masuk terlebih dahulu ke gudang).
3.  Sistem memotong kuantitas dari batch tertua/terdekat kedaluwarsa tersebut secara iteratif hingga jumlah permintaan barang keluar terpenuhi.
4.  Setiap potongan dicatat ke dalam tabel `outgoing_good_details` untuk melacak asal usul batch, sehingga nilai Harga Pokok Penjualan (HPP / COGS) dapat terhitung akurat berdasarkan harga beli asli dari batch tersebut.

### B. Kalkulasi Safety Stock & Reorder Point (ROP) (`SafetyStockService.php`)
Perhitungan ini dilakukan berdasarkan data penjualan historis 30 hari terakhir. Formula yang diimplementasikan adalah:

$$\text{Safety Stock (SS)} = (\text{Penjualan Harian Maksimum} - \text{Rata-rata Penjualan Harian}) \times \text{Lead Time}$$

$$\text{Reorder Point (ROP)} = (\text{Rata-rata Penjualan Harian} \times \text{Lead Time}) + \text{Safety Stock}$$

*   **Lead Time**: Waktu tunggu pengiriman barang dari supplier hingga sampai di gudang (secara default diset selama 3 hari).
*   **Penjualan Harian Maksimum**: Volume penjualan harian tertinggi dari produk tersebut selama periode 30 hari terakhir.
*   **Rata-rata Penjualan Harian**: Total volume penjualan dibagi jumlah hari analisis (30 hari).
*   **Penentuan Status Stok**:
    *   Stok Saat Ini $\le$ ROP $\rightarrow$ **Status: `Order`** (Harus segera memesan ulang ke supplier).
    *   ROP $<$ Stok Saat Ini $\le$ (ROP + Safety Stock) $\rightarrow$ **Status: `Warning`** (Stok menipis, bersiap melakukan pemesanan).
    *   Stok Saat Ini $>$ (ROP + Safety Stock) $\rightarrow$ **Status: `Aman`** (Stok melimpah dan aman untuk operasional).
*   Sistem secara otomatis menghasilkan kalimat rekomendasi dalam Bahasa Indonesia yang memberi tahu kuantitas pesanan minimum yang disarankan.

### C. Alur Pengisian & Penyesuaian Stok Opname
Ketika staff melakukan Stock Opname:
1.  Sistem membandingkan angka input fisik (`stok_fisik`) dengan angka sistem (`stok_sistem`).
2.  Jika terjadi **Selisih Kurang** (Stok fisik < Stok sistem):
    *   Sistem secara otomatis membuat transaksi barang keluar dengan jenis `penyesuaian` dan memotong batch stok menggunakan algoritma FIFO.
3.  Jika terjadi **Selisih Lebih** (Stok fisik > Stok sistem):
    *   Sistem membuat transaksi barang masuk dengan kode batch khusus penyesuaian opname (`BM-YYYYMMDD-XXXX`), membuat record batch baru, dan menambah stok sistem agar sesuai dengan kondisi fisik riil.

---

## 5. FITUR EKSPOR, IMPOR & LAPORAN DINAMIS

### A. Fitur Impor Data
*   **Impor Penjualan (Excel/CSV)**: Memungkinkan Manager mengunggah laporan penjualan eksternal. Sistem akan memvalidasi baris demi baris (mengecek kesesuaian format tanggal, nama produk, dan validitas kuantitas) dan menyimpannya ke database. Setelah diimpor, sistem secara otomatis memicu kalkulasi ulang Safety Stock & ROP untuk seluruh produk.
*   **Impor Faktur Pembelian (Excel/CSV)**: Memungkinkan Admin/Manager mengimpor faktur pembelian dari supplier. Produk baru yang belum ada di database akan dibuat otomatis dengan kode produk ter-generate, sedangkan produk yang sudah ada akan ditambahkan stoknya ke dalam batch baru.

### B. Keamanan Dinamis Laporan (Dynamic Column Masking)
Halaman `Laporan Penjualan` (dan file unduhannya) memproteksi kolom keuangan secara dinamis di level controller:
```php
$role = auth()->user()->role ?? 'admin';
$isOwner = ($role === 'owner');

// Pada file cetak PDF dan Excel:
if ($isOwner) {
    // Render kolom Harga Jual, Omzet, Laba, dan Margin
} else {
    // Sembunyikan kolom finansial, hanya tampilkan Kuantitas Penjualan (Pcs)
}
```
Metode ini mencegah kebocoran informasi profit margin perusahaan kepada staf operasional tanpa menghalangi tugas mereka untuk melihat kuantitas produk yang laku terjual.

---

## 6. CARA MENJALANKAN & DATA LOGIN DEFAULT

### Akun Login Bawaan (Default Seeder)
Gunakan kredensial berikut untuk menguji sistem berdasarkan masing-masing peran setelah melakukan *seeding* database:

1.  **Administrator**:
    *   **Email**: `admin@della.test`
    *   **Password**: `password`
2.  **Manager**:
    *   **Email**: `manager@della.test`
    *   **Password**: `password`
3.  **Owner**:
    *   **Email**: `owner@della.test`
    *   **Password**: `password`

### Panduan Cepat Menjalankan Aplikasi
1.  Pastikan **XAMPP** berjalan (Apache & MySQL).
2.  Aktifkan ekstensi `gd`, `zip`, `fileinfo`, dan `intl` di file `php.ini` XAMPP Anda.
3.  Buat database kosong bernama `della_frozenmart` melalui phpMyAdmin.
4.  Buka terminal/command prompt di folder proyek ini:
    ```powershell
    # Copy file konfigurasi env
    copy .env.example .env
    
    # Instal library php
    composer install
    
    # Generate key aplikasi
    php artisan key:generate
    
    # Jalankan migrasi & isi data awal (seeder)
    php artisan migrate --seed
    
    # Jalankan server lokal
    php artisan serve
    ```
5.  Akses aplikasi melalui browser di: [http://127.0.0.1:8000](http://127.0.0.1:8000).

---
*Dokumentasi ini disusun sebagai acuan teknis operasional pengembangan Sistem Informasi Persediaan Della Frozen Mart.*
