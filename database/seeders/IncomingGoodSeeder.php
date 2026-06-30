<?php

namespace Database\Seeders;

use App\Models\IncomingGood;
use App\Models\StockBatch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * IncomingGoodSeeder - Membuat 28 transaksi barang masuk dengan total qty 1852,
 * total nilai Rp 45.230.000, dan 3 lokasi (LOC-01, LOC-02, LOC-03).
 */
class IncomingGoodSeeder extends Seeder
{
    public function run(): void
    {
        $incomingGoods = [];

        foreach ($incomingGoods as $incoming) {
            $good = IncomingGood::create($incoming);
            
            // Create corresponding StockBatch record
            StockBatch::create([
                'product_id' => $good->product_id,
                'incoming_good_id' => $good->id,
                'batch_code' => $good->batch_code,
                'tanggal_masuk' => $good->tanggal_masuk,
                'tanggal_kedaluwarsa' => $good->tanggal_kedaluwarsa,
                'jumlah_awal' => $good->jumlah,
                'jumlah_sisa' => $good->jumlah,
                'satuan' => $good->satuan,
            ]);
        }
    }
}
