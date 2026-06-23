<?php

namespace Database\Seeders;

use App\Models\StockNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * NotificationSeeder - Notifikasi sampel untuk admin dan manager.
 *
 * Jenis notifikasi:
 * - Peringatan stok warning
 * - Peringatan stok order (kritis)
 * - Import berhasil
 * - Produk mendekati kedaluwarsa
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            // Stok Warning
            [
                'user_id' => 1,
                'product_id' => 12, // Meru Lapis Bogor
                'judul' => 'Peringatan Stok Rendah',
                'pesan' => 'Stok produk Meru Lapis Bogor (PRD-0012) saat ini 25 unit, mendekati batas minimum 10 unit. Segera lakukan pemesanan ulang.',
                'status_baca' => false,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            // Stok Order (Kritis)
            [
                'user_id' => 1,
                'product_id' => 16, // Mazzoni BBQ
                'judul' => 'Stok Kritis - Perlu Pemesanan Segera',
                'pesan' => 'Stok produk Mazzoni BBQ 250 Gr (PRD-0016) saat ini hanya 20 unit dan berada di bawah Reorder Point. Segera buat Purchase Order ke supplier.',
                'status_baca' => false,
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(4),
            ],
            // Import Berhasil
            [
                'user_id' => 1,
                'product_id' => null,
                'judul' => 'Import Faktur Pembelian Berhasil',
                'pesan' => 'File "Ekspor_Faktur_Pembelian_17_05_26_15_15.xlsx" berhasil diimport. Total 12 baris data, 12 berhasil, 0 gagal.',
                'status_baca' => true,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            // Produk Mendekati Kedaluwarsa
            [
                'user_id' => 1,
                'product_id' => 10, // Jamur Enoki
                'judul' => 'Produk Mendekati Kedaluwarsa',
                'pesan' => 'Batch stok Jamur Enoki (PRD-0010) dengan kode batch BM-20260518-0004 akan kedaluwarsa dalam 14 hari. Segera lakukan rotasi stok atau penjualan.',
                'status_baca' => false,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            // Notifikasi untuk Manager - Stok Warning
            [
                'user_id' => 2,
                'product_id' => 2, // Slice Ribeye
                'judul' => 'Peringatan Stok Rendah',
                'pesan' => 'Stok produk Slice Ribeye 500 Gr (PRD-0002) saat ini 45 unit. Perhatikan tren penjualan untuk menentukan waktu pemesanan ulang.',
                'status_baca' => false,
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(8),
            ],
            // Notifikasi untuk Manager - Laporan Analisis
            [
                'user_id' => 2,
                'product_id' => null,
                'judul' => 'Analisis Safety Stock Selesai',
                'pesan' => 'Analisis Safety Stock untuk 12 produk telah selesai dilakukan. Silakan cek halaman Analisis Persediaan untuk melihat hasil perhitungan dan rekomendasi.',
                'status_baca' => true,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($notifications as $notification) {
            StockNotification::create($notification);
        }
    }
}
