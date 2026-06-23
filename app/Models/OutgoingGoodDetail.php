<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OutgoingGoodDetail - Detail barang keluar per batch.
 *
 * Menyimpan informasi batch mana yang diambil stoknya (FIFO)
 * untuk setiap transaksi barang keluar.
 */
class OutgoingGoodDetail extends Model
{
    use HasFactory;

    protected $table = 'detail_barang_keluar';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'outgoing_good_id',
        'stock_batch_id',
        'jumlah_diambil',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function outgoingGood(): BelongsTo
    {
        return $this->belongsTo(OutgoingGood::class);
    }

    public function stockBatch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class);
    }
}
