<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockBatch extends Model
{
    use HasFactory;

    protected $table = 'batch_stok';

    protected $fillable = [
        'product_id',
        'incoming_good_id',
        'batch_code',
        'tanggal_masuk',
        'tanggal_kedaluwarsa',
        'jumlah_awal',
        'jumlah_sisa',
        'satuan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_kedaluwarsa' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function incomingGood(): BelongsTo
    {
        return $this->belongsTo(IncomingGood::class);
    }

    public function outgoingGoodDetails(): HasMany
    {
        return $this->hasMany(OutgoingGoodDetail::class);
    }
}
