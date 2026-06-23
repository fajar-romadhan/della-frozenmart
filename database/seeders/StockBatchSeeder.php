<?php

namespace Database\Seeders;

use App\Models\IncomingGood;
use App\Models\StockBatch;
use Illuminate\Database\Seeder;

/**
 * StockBatchSeeder - Membuat batch stok dari setiap barang masuk.
 *
 * Setiap IncomingGood mendapatkan 1 StockBatch.
 * jumlah_sisa = jumlah_awal (stok baru belum terpakai).
 */
class StockBatchSeeder extends Seeder
{
    public function run(): void
    {
        $incomingGoods = IncomingGood::all();

        foreach ($incomingGoods as $incoming) {
            StockBatch::create([
                'product_id' => $incoming->product_id,
                'incoming_good_id' => $incoming->id,
                'batch_code' => $incoming->batch_code,
                'tanggal_masuk' => $incoming->tanggal_masuk,
                'tanggal_kedaluwarsa' => $incoming->tanggal_kedaluwarsa,
                'jumlah_awal' => $incoming->jumlah,
                'jumlah_sisa' => $incoming->jumlah, // Stok baru, belum ada pengurangan
                'satuan' => $incoming->satuan,
            ]);
        }
    }
}
