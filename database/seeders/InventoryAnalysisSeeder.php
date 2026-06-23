<?php

namespace Database\Seeders;

use App\Models\InventoryAnalysis;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * InventoryAnalysisSeeder - Hasil analisis Safety Stock untuk 12 produk.
 *
 * Rumus:
 * - Safety Stock = (max_sales - average_usage) × lead_time
 * - ROP (Reorder Point) = (average_usage × lead_time) + safety_stock
 *
 * Status stok:
 * - Aman: stok_saat_ini > ROP
 * - Warning: safety_stock < stok_saat_ini <= ROP
 * - Order: stok_saat_ini <= safety_stock
 */
class InventoryAnalysisSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $analyses = [
            // ===============================================================
            // PRD-0001: Okey Sosis 500 Gr (Best Seller - Aman)
            // ===============================================================
            [
                'product_id' => 1,
                'weekday_sales' => 6.00,
                'weekend_sales' => 11.50,
                'event_sales' => 21.50,
                'average_usage' => 7.80,
                'max_sales' => 25.00,
                'lead_time' => 3,
                'safety_stock' => 51.60,  // (25 - 7.8) * 3
                'reorder_point' => 75.00, // (7.8 * 3) + 51.6
                'stok_saat_ini' => 120,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Tidak perlu melakukan pemesanan ulang saat ini.',
            ],
            // ===============================================================
            // PRD-0007: Champ Sosis Ayam (Top Seller - Aman)
            // ===============================================================
            [
                'product_id' => 7,
                'weekday_sales' => 7.50,
                'weekend_sales' => 14.00,
                'event_sales' => 25.00,
                'average_usage' => 9.80,
                'max_sales' => 30.00,
                'lead_time' => 3,
                'safety_stock' => 60.60,  // (30 - 9.8) * 3
                'reorder_point' => 90.00, // (9.8 * 3) + 60.6
                'stok_saat_ini' => 200,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Produk best seller, pantau penjualan harian.',
            ],
            // ===============================================================
            // PRD-0009: Fiesta Chicken Nugget (Warning)
            // ===============================================================
            [
                'product_id' => 9,
                'weekday_sales' => 5.00,
                'weekend_sales' => 9.50,
                'event_sales' => 17.50,
                'average_usage' => 6.50,
                'max_sales' => 20.00,
                'lead_time' => 3,
                'safety_stock' => 40.50,  // (20 - 6.5) * 3
                'reorder_point' => 60.00, // (6.5 * 3) + 40.5
                'stok_saat_ini' => 150,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Perhatikan tren penjualan akhir pekan yang tinggi.',
            ],
            // ===============================================================
            // PRD-0010: Jamur Enoki (High Volume - Warning)
            // ===============================================================
            [
                'product_id' => 10,
                'weekday_sales' => 11.50,
                'weekend_sales' => 20.00,
                'event_sales' => 35.00,
                'average_usage' => 14.20,
                'max_sales' => 40.00,
                'lead_time' => 3,
                'safety_stock' => 77.40,  // (40 - 14.2) * 3
                'reorder_point' => 120.00, // (14.2 * 3) + 77.4
                'stok_saat_ini' => 300,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Volume penjualan tinggi, pastikan rotasi FIFO berjalan baik karena masa simpan pendek.',
            ],
            // ===============================================================
            // PRD-0015: Fiesta Kentang 1 Kg (Aman)
            // ===============================================================
            [
                'product_id' => 15,
                'weekday_sales' => 4.50,
                'weekend_sales' => 8.00,
                'event_sales' => 15.00,
                'average_usage' => 5.60,
                'max_sales' => 18.00,
                'lead_time' => 3,
                'safety_stock' => 37.20,  // (18 - 5.6) * 3
                'reorder_point' => 54.00, // (5.6 * 3) + 37.2
                'stok_saat_ini' => 100,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Tidak perlu melakukan pemesanan ulang.',
            ],
            // ===============================================================
            // PRD-0017: Cimory Yogurt Drink (Aman)
            // ===============================================================
            [
                'product_id' => 17,
                'weekday_sales' => 6.00,
                'weekend_sales' => 11.00,
                'event_sales' => 0.00,
                'average_usage' => 7.20,
                'max_sales' => 14.00,
                'lead_time' => 3,
                'safety_stock' => 20.40,  // (14 - 7.2) * 3
                'reorder_point' => 42.00, // (7.2 * 3) + 20.4
                'stok_saat_ini' => 180,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Perhatikan tanggal kedaluwarsa karena masa simpan pendek.',
            ],
            // ===============================================================
            // PRD-0012: Meru Lapis Bogor (Warning - stok rendah)
            // ===============================================================
            [
                'product_id' => 12,
                'weekday_sales' => 1.50,
                'weekend_sales' => 2.00,
                'event_sales' => 0.00,
                'average_usage' => 1.60,
                'max_sales' => 3.00,
                'lead_time' => 3,
                'safety_stock' => 4.20,   // (3 - 1.6) * 3
                'reorder_point' => 9.00,  // (1.6 * 3) + 4.2
                'stok_saat_ini' => 25,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Produk slow mover, sesuaikan jumlah pemesanan.',
            ],
            // ===============================================================
            // PRD-0008: Cedea Otak-Otak Singapur (Warning)
            // ===============================================================
            [
                'product_id' => 8,
                'weekday_sales' => 2.00,
                'weekend_sales' => 3.50,
                'event_sales' => 0.00,
                'average_usage' => 2.40,
                'max_sales' => 5.00,
                'lead_time' => 3,
                'safety_stock' => 7.80,   // (5 - 2.4) * 3
                'reorder_point' => 15.00, // (2.4 * 3) + 7.8
                'stok_saat_ini' => 35,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Pantau penjualan akhir pekan.',
            ],
            // ===============================================================
            // PRD-0016: Mazzoni BBQ 250 Gr (Order - stok kritis)
            // ===============================================================
            [
                'product_id' => 16,
                'weekday_sales' => 1.00,
                'weekend_sales' => 1.50,
                'event_sales' => 0.00,
                'average_usage' => 1.10,
                'max_sales' => 2.00,
                'lead_time' => 3,
                'safety_stock' => 2.70,   // (2 - 1.1) * 3
                'reorder_point' => 6.00,  // (1.1 * 3) + 2.7
                'stok_saat_ini' => 20,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Produk slow mover.',
            ],
            // ===============================================================
            // PRD-0005: Sallam Sosis Mini (Moderate)
            // ===============================================================
            [
                'product_id' => 5,
                'weekday_sales' => 3.50,
                'weekend_sales' => 6.00,
                'event_sales' => 0.00,
                'average_usage' => 4.20,
                'max_sales' => 8.00,
                'lead_time' => 3,
                'safety_stock' => 11.40,  // (8 - 4.2) * 3
                'reorder_point' => 24.00, // (4.2 * 3) + 11.4
                'stok_saat_ini' => 90,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Tidak perlu melakukan pemesanan ulang.',
            ],
            // ===============================================================
            // PRD-0018: Yakult Ori (Moderate)
            // ===============================================================
            [
                'product_id' => 18,
                'weekday_sales' => 3.50,
                'weekend_sales' => 7.50,
                'event_sales' => 0.00,
                'average_usage' => 4.60,
                'max_sales' => 10.00,
                'lead_time' => 3,
                'safety_stock' => 16.20,  // (10 - 4.6) * 3
                'reorder_point' => 30.00, // (4.6 * 3) + 16.2
                'stok_saat_ini' => 100,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Perhatikan tanggal kedaluwarsa.',
            ],
            // ===============================================================
            // PRD-0019: Indoprima Dimsum (Moderate)
            // ===============================================================
            [
                'product_id' => 19,
                'weekday_sales' => 3.00,
                'weekend_sales' => 5.00,
                'event_sales' => 0.00,
                'average_usage' => 3.60,
                'max_sales' => 7.00,
                'lead_time' => 3,
                'safety_stock' => 10.20,  // (7 - 3.6) * 3
                'reorder_point' => 21.00, // (3.6 * 3) + 10.2
                'stok_saat_ini' => 85,
                'status_stok' => 'Aman',
                'rekomendasi' => 'Stok aman. Tidak perlu melakukan pemesanan ulang.',
            ],
        ];

        foreach ($analyses as $analysis) {
            InventoryAnalysis::create(array_merge($analysis, [
                'tanggal_analisis' => $today->toDateString(),
                'user_id' => 1,
            ]));
        }
    }
}
