<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncomingGood extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'tanggal_masuk',
        'jumlah',
        'satuan',
        'harga_beli',
        'tanggal_kedaluwarsa',
        'batch_code',
        'sumber_import',
        'nama_file_import',
        'id_lokasi',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_kedaluwarsa' => 'date',
            'harga_beli' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockBatch(): HasOne
    {
        return $this->hasOne(StockBatch::class);
    }
}
