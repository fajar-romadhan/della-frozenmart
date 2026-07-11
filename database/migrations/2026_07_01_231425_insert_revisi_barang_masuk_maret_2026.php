<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Insert barang masuk (incoming goods) Jan-Mei 2026 untuk 9 produk dari data manual.
     * Nama produk 100% konsisten dengan 31 produk resmi (ProductSeeder).
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Define Suppliers
        $suppliers = [
            ['nama_supplier' => 'CV Kylafood Nusantara',   'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Roker Jaya Frozen',    'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Sony Frozen Food',     'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Susan Wilson Reseller','status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'Hijrafood',               'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Belfoods',             'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Champ Citra Mandiri',  'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Siomy Makmur',         'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        // 2. Produk yang akan di-map (nama PERSIS sesuai ProductSeeder & 31 produk resmi)
        $products = [
            ['nama_produk' => 'Sosis okay 500g',      'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Fiesta Karage 450 gr', 'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'jamur enoki',           'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Meru Lapis Bogor',      'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Cireng Rujak 15gr',     'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Sallam Nugget 500 gr',  'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'WARISAN ISI 50',         'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Belfood Sosis Isi 30',  'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Richees Nugget',         'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10, 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        // 3. Transaksi barang masuk Jan-Mei 2026 (nama produk persis sama dgn di atas)
        $transactions = [
            // Januari
            ['tanggal_masuk' => '2026-01-03', 'product_name' => 'Sosis okay 500g',      'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-14', 'product_name' => 'Sosis okay 500g',      'jumlah' => 145, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-27', 'product_name' => 'Sosis okay 500g',      'jumlah' => 175, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-06', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 185, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-01-21', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 215, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-01-05', 'product_name' => 'jamur enoki',           'jumlah' => 140, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-01-16', 'product_name' => 'jamur enoki',           'jumlah' => 110, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-01-29', 'product_name' => 'jamur enoki',           'jumlah' => 150, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-01-08', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 90,  'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-01-25', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 110, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-01-07', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 175, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-01-24', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 225, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-01-02', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 190, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-15', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 140, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-28', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 170, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-01-09', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 260, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-01-26', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 240, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-01-10', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 130, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-01-20', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 120, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-01-31', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-01-05', 'product_name' => 'Richees Nugget',         'jumlah' => 210, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-01-17', 'product_name' => 'Richees Nugget',         'jumlah' => 250, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-01-29', 'product_name' => 'Richees Nugget',         'jumlah' => 240, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            // Februari
            ['tanggal_masuk' => '2026-02-01', 'product_name' => 'Sosis okay 500g',      'jumlah' => 120, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-02-16', 'product_name' => 'Sosis okay 500g',      'jumlah' => 100, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-02-04', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 150, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-02-20', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 120, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-02-02', 'product_name' => 'jamur enoki',           'jumlah' => 90,  'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-02-14', 'product_name' => 'jamur enoki',           'jumlah' => 80,  'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-02-26', 'product_name' => 'jamur enoki',           'jumlah' => 100, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-02-05', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 70,  'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-02-22', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 80,  'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-02-06', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 140, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-02-24', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 120, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-02-01', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 130, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-02-17', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 110, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-02-08', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 180, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-02-25', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 150, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-02-09', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 100, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-02-19', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 90,  'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-02-27', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 80,  'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-02-07', 'product_name' => 'Richees Nugget',         'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-02-19', 'product_name' => 'Richees Nugget',         'jumlah' => 300, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            // Maret
            ['tanggal_masuk' => '2026-03-01', 'product_name' => 'Sosis okay 500g',      'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-05', 'product_name' => 'Sosis okay 500g',      'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-10', 'product_name' => 'Sosis okay 500g',      'jumlah' => 300, 'harga_beli' => 18500.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-02', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 200, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-03-05', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 240, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-03-10', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 100, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-03-02', 'product_name' => 'jamur enoki',           'jumlah' => 170, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-03-05', 'product_name' => 'jamur enoki',           'jumlah' => 100, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-03-10', 'product_name' => 'jamur enoki',           'jumlah' => 300, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-03-03', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 140, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-03-08', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 210, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-03-14', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 100, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-03-05', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 180, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-03-13', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 220, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-03-28', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 100, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-03-02', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 190, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-10', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 300, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-29', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 260, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-03-01', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 150, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-03-06', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 170, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-03-12', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 300, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-03-02', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 140, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-03-10', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 300, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-03-29', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 220, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-03-02', 'product_name' => 'Richees Nugget',         'jumlah' => 170, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-03-09', 'product_name' => 'Richees Nugget',         'jumlah' => 400, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            // April
            ['tanggal_masuk' => '2026-04-02', 'product_name' => 'Sosis okay 500g',      'jumlah' => 170, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-16', 'product_name' => 'Sosis okay 500g',      'jumlah' => 130, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-28', 'product_name' => 'Sosis okay 500g',      'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-04', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 170, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-04-22', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 180, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-04-03', 'product_name' => 'jamur enoki',           'jumlah' => 120, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-04-15', 'product_name' => 'jamur enoki',           'jumlah' => 100, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-04-27', 'product_name' => 'jamur enoki',           'jumlah' => 110, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-04-07', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 110, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-04-24', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 100, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-04-06', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 160, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-04-23', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 170, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-04-01', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 170, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-17', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-29', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 130, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-04-09', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 210, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-04-25', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 190, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-04-10', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 120, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-04-18', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 130, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-04-30', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 110, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-04-08', 'product_name' => 'Richees Nugget',         'jumlah' => 220, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-04-21', 'product_name' => 'Richees Nugget',         'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            // Mei
            ['tanggal_masuk' => '2026-05-02', 'product_name' => 'Sosis okay 500g',      'jumlah' => 200, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-08', 'product_name' => 'Sosis okay 500g',      'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-14', 'product_name' => 'Sosis okay 500g',      'jumlah' => 220, 'harga_beli' => 18500.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-20', 'product_name' => 'Sosis okay 500g',      'jumlah' => 300, 'harga_beli' => 18500.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-04', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 200, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-05-21', 'product_name' => 'Fiesta Karage 450 gr', 'jumlah' => 220, 'harga_beli' => 42000.0, 'supplier_name' => 'PT Champ Citra Mandiri'],
            ['tanggal_masuk' => '2026-05-03', 'product_name' => 'jamur enoki',           'jumlah' => 140, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-05-12', 'product_name' => 'jamur enoki',           'jumlah' => 130, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-05-21', 'product_name' => 'jamur enoki',           'jumlah' => 250, 'harga_beli' => 5000.0,  'supplier_name' => 'Hijrafood'],
            ['tanggal_masuk' => '2026-05-06', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 130, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-05-24', 'product_name' => 'Meru Lapis Bogor',      'jumlah' => 150, 'harga_beli' => 35000.0, 'supplier_name' => 'CV Susan Wilson Reseller'],
            ['tanggal_masuk' => '2026-05-03', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 180, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-05-11', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 180, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-05-20', 'product_name' => 'Cireng Rujak 15gr',     'jumlah' => 200, 'harga_beli' => 13000.0, 'supplier_name' => 'CV Roker Jaya Frozen'],
            ['tanggal_masuk' => '2026-05-01', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 190, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-11', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-21', 'product_name' => 'Sallam Nugget 500 gr',  'jumlah' => 180, 'harga_beli' => 18000.0, 'supplier_name' => 'CV Sony Frozen Food'],
            ['tanggal_masuk' => '2026-05-02', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 160, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-05-08', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 100, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-05-14', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 250, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-05-20', 'product_name' => 'WARISAN ISI 50',         'jumlah' => 250, 'harga_beli' => 28000.0, 'supplier_name' => 'CV Kylafood Nusantara'],
            ['tanggal_masuk' => '2026-05-03', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 300, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-05-20', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 300, 'harga_beli' => 18900.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-05-31', 'product_name' => 'Belfood Sosis Isi 30',  'jumlah' => 150, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Belfoods'],
            ['tanggal_masuk' => '2026-05-08', 'product_name' => 'Richees Nugget',         'jumlah' => 250, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-05-19', 'product_name' => 'Richees Nugget',         'jumlah' => 400, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
            ['tanggal_masuk' => '2026-05-31', 'product_name' => 'Richees Nugget',         'jumlah' => 260, 'harga_beli' => 18000.0, 'supplier_name' => 'PT Siomy Makmur'],
        ];

        // Find or create admin user ID
        $admin = DB::table('pengguna')->orderBy('id')->first();
        $userId = $admin ? $admin->id : 1;

        // Insert Suppliers and build name -> ID map
        $supplierMap = [];
        foreach ($suppliers as $s) {
            $existing = DB::table('supplier')->where('nama_supplier', $s['nama_supplier'])->first();
            if ($existing) {
                $supplierMap[$s['nama_supplier']] = $existing->id;
            } else {
                $id = DB::table('supplier')->insertGetId($s);
                $supplierMap[$s['nama_supplier']] = $id;
            }
        }

        // Insert Products (if not already seeded) and build name -> ID map
        $productMap = [];
        foreach ($products as $p) {
            $existing = DB::table('produk')->where('nama_produk', $p['nama_produk'])->first();
            if ($existing) {
                $productMap[$p['nama_produk']] = $existing->id;
            } else {
                $lastProduct = DB::table('produk')->orderByRaw("CAST(SUBSTRING(kode_produk, 5) AS UNSIGNED) DESC")->first();
                $nextNumber = 1;
                if ($lastProduct && preg_match('/PRD-(\d+)/', $lastProduct->kode_produk, $matches)) {
                    $nextNumber = (int) $matches[1] + 1;
                }
                $p['kode_produk'] = 'PRD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $id = DB::table('produk')->insertGetId($p);
                $productMap[$p['nama_produk']] = $id;
            }
        }

        // Insert Transactions
        $todayCounts = [];
        foreach ($transactions as $t) {
            $productId = $productMap[$t['product_name']] ?? null;
            $supplierId = $supplierMap[$t['supplier_name']] ?? null;

            if (!$productId) {
                continue;
            }

            $expiry = Carbon::parse($t['tanggal_masuk'])->addMonths(6)->toDateString();
            $dateKey = str_replace('-', '', $t['tanggal_masuk']);
            if (!isset($todayCounts[$t['tanggal_masuk']])) {
                $todayCounts[$t['tanggal_masuk']] = DB::table('barang_masuk')->whereDate('tanggal_masuk', $t['tanggal_masuk'])->count();
            }
            $todayCounts[$t['tanggal_masuk']]++;
            $batchCode = 'BM-' . $dateKey . '-' . str_pad($todayCounts[$t['tanggal_masuk']], 4, '0', STR_PAD_LEFT);

            $incomingId = DB::table('barang_masuk')->insertGetId([
                'product_id'          => $productId,
                'supplier_id'         => $supplierId,
                'tanggal_masuk'       => $t['tanggal_masuk'],
                'jumlah'              => $t['jumlah'],
                'satuan'              => 'PCS',
                'harga_beli'          => $t['harga_beli'],
                'tanggal_kedaluwarsa' => $expiry,
                'batch_code'          => $batchCode,
                'sumber_import'       => 'Revisi Maret 2026',
                'id_lokasi'           => 'Gudang Utama',
                'keterangan'          => 'Import Otomatis Data Jan-Mei 2026',
                'user_id'             => $userId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('batch_stok')->insert([
                'product_id'          => $productId,
                'incoming_good_id'    => $incomingId,
                'batch_code'          => $batchCode,
                'tanggal_masuk'       => $t['tanggal_masuk'],
                'tanggal_kedaluwarsa' => $expiry,
                'jumlah_awal'         => $t['jumlah'],
                'jumlah_sisa'         => $t['jumlah'],
                'satuan'              => 'PCS',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('produk')->where('id', $productId)->increment('stok_saat_ini', $t['jumlah']);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        $incomingGoods = DB::table('barang_masuk')->where('sumber_import', 'Revisi Maret 2026')->get();
        foreach ($incomingGoods as $ig) {
            DB::table('produk')->where('id', $ig->product_id)->decrement('stok_saat_ini', $ig->jumlah);
            DB::table('batch_stok')->where('incoming_good_id', $ig->id)->delete();
        }
        DB::table('barang_masuk')->where('sumber_import', 'Revisi Maret 2026')->delete();

        Schema::enableForeignKeyConstraints();
    }
};
