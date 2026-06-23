<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Sale - Data penjualan harian.
 *
 * Digunakan sebagai basis data untuk perhitungan Safety Stock
 * (weekday sales, weekend sales, event sales).
 */
class Sale extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'tanggal_penjualan',
        'jumlah_terjual',
        'sumber_import',
        'nama_file_import',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_penjualan' => 'date',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
