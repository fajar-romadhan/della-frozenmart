<?php

namespace Database\Seeders;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * SaleSeeder - Data penjualan 30 hari terakhir untuk analisis Safety Stock.
 *
 * Variasi kuantitas berdasarkan:
 * - Hari kerja (weekday): penjualan normal
 * - Akhir pekan (weekend): penjualan lebih tinggi
 * - Event tertentu: penjualan melonjak
 *
 * Produk best-seller: Champ Sosis (7), Okey Sosis (1), Fiesta Kentang (15), Fiesta Nugget (9)
 * Produk slow-moving: Meru Lapis (12), Mazzoni BBQ (16), Saus BBQ (14)
 */
class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $sales = [];

        // =====================================================================
        // Produk 1: Okey Sosis 500 Gr (Best Seller)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(8, 15) : rand(4, 8);
            // Event boost di hari ke-15 dan ke-20
            if ($i === 15 || $i === 20) {
                $qty = rand(18, 25);
            }
            $sales[] = [
                'product_id' => 1,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 7: Champ Sosis Ayam 375 Gr (Top Seller)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(10, 18) : rand(5, 10);
            if ($i === 10 || $i === 22) {
                $qty = rand(20, 30);
            }
            $sales[] = [
                'product_id' => 7,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 9: Fiesta Chicken Nugget 450 Gr (Popular)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(7, 12) : rand(3, 7);
            if ($i === 5) {
                $qty = rand(15, 20);
            }
            $sales[] = [
                'product_id' => 9,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 15: Fiesta Kentang 1 Kg (Popular)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(6, 10) : rand(3, 6);
            if ($i === 8) {
                $qty = rand(12, 18);
            }
            $sales[] = [
                'product_id' => 15,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 17: Cimory Yogurt Drink (Steady)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(8, 14) : rand(4, 8);
            $sales[] = [
                'product_id' => 17,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 18: Yakult Ori (Steady)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(5, 10) : rand(2, 5);
            $sales[] = [
                'product_id' => 18,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 10: Jamur Enoki (High Volume)
        // =====================================================================
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(15, 25) : rand(8, 15);
            if ($i === 12) {
                $qty = rand(30, 40);
            }
            $sales[] = [
                'product_id' => 10,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 5: Sallam Sosis Mini (Moderate)
        // =====================================================================
        for ($i = 29; $i >= 0; $i -= 2) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(4, 8) : rand(2, 5);
            $sales[] = [
                'product_id' => 5,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 12: Meru Lapis Bogor (Slow Mover)
        // =====================================================================
        for ($i = 28; $i >= 0; $i -= 4) {
            $date = $today->copy()->subDays($i);
            $qty = rand(1, 3);
            $sales[] = [
                'product_id' => 12,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 16: Mazzoni BBQ 250 Gr (Slow Mover)
        // =====================================================================
        for ($i = 27; $i >= 0; $i -= 5) {
            $date = $today->copy()->subDays($i);
            $qty = rand(1, 2);
            $sales[] = [
                'product_id' => 16,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 4: Cedea Dumpling Ayam (Moderate)
        // =====================================================================
        for ($i = 29; $i >= 0; $i -= 2) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(3, 6) : rand(1, 4);
            $sales[] = [
                'product_id' => 4,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // =====================================================================
        // Produk 19: Indoprima Dimsum (Moderate)
        // =====================================================================
        for ($i = 29; $i >= 0; $i -= 2) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $qty = $isWeekend ? rand(3, 7) : rand(2, 4);
            $sales[] = [
                'product_id' => 19,
                'tanggal_penjualan' => $date->toDateString(),
                'jumlah_terjual' => $qty,
                'user_id' => 1,
            ];
        }

        // Insert semua data penjualan
        foreach ($sales as $sale) {
            Sale::create(array_merge($sale, [
                'sumber_import' => null,
                'nama_file_import' => null,
            ]));
        }
    }
}
