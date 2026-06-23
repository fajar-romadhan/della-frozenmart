<?php

namespace App\Services;

use App\Models\OutgoingGoodDetail;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class FifoService
{
    /**
     * Deduct stock using FIFO method (First Expired, First Out / First In, First Out).
     *
     * Prioritizes batches by:
     * 1. Nearest expiry date (NULLS LAST)
     * 2. Oldest entry date (FIFO)
     *
     * @param int $productId
     * @param int $quantity
     * @param int $outgoingGoodId
     * @return array Details of batches used
     * @throws \Exception if insufficient stock
     */
    public function deductStock(int $productId, int $quantity, int $outgoingGoodId): array
    {
        return DB::transaction(function () use ($productId, $quantity, $outgoingGoodId) {
            $product = Product::findOrFail($productId);

            // Get available batches ordered by FEFO then FIFO
            $batches = StockBatch::where('product_id', $productId)
                ->where('jumlah_sisa', '>', 0)
                ->orderByRaw('CASE WHEN tanggal_kedaluwarsa IS NULL THEN 1 ELSE 0 END')
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->orderBy('tanggal_masuk', 'asc')
                ->lockForUpdate()
                ->get();

            $remaining = $quantity;
            $details = [];

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($remaining, $batch->jumlah_sisa);

                // Create outgoing good detail
                $detail = OutgoingGoodDetail::create([
                    'outgoing_good_id' => $outgoingGoodId,
                    'stock_batch_id' => $batch->id,
                    'jumlah_diambil' => $take,
                ]);

                // Reduce batch remaining stock
                $batch->jumlah_sisa -= $take;
                $batch->save();

                $details[] = [
                    'detail_id' => $detail->id,
                    'stock_batch_id' => $batch->id,
                    'batch_code' => $batch->batch_code,
                    'tanggal_kedaluwarsa' => $batch->tanggal_kedaluwarsa?->format('d/m/Y'),
                    'jumlah_diambil' => $take,
                    'sisa_batch' => $batch->jumlah_sisa,
                ];

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \Exception(
                    "Stok tidak mencukupi untuk produk '{$product->nama_produk}'. " .
                    "Dibutuhkan: {$quantity}, Tersedia: " . ($quantity - $remaining) . "."
                );
            }

            // Update product current stock
            $product->stok_saat_ini -= $quantity;
            $product->save();

            return $details;
        });
    }
}
