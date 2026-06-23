<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutgoingGood extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';

    protected $fillable = [
        'product_id',
        'tanggal_keluar',
        'jumlah',
        'jenis_keluar',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outgoingGoodDetails(): HasMany
    {
        return $this->hasMany(OutgoingGoodDetail::class);
    }
}
