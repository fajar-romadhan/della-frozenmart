<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model InventoryAnalysis - Hasil analisis persediaan Safety Stock.
 *
 * Menyimpan hasil perhitungan:
 * - Safety Stock = (max_sales - average_usage) × lead_time
 * - ROP (Reorder Point) = (average_usage × lead_time) + safety_stock
 * - Status stok: Aman, Warning, atau Order
 */
class InventoryAnalysis extends Model
{
    use HasFactory;

    protected $table = 'analisa_persediaan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'weekday_sales',
        'weekend_sales',
        'event_sales',
        'average_usage',
        'max_sales',
        'lead_time',
        'safety_stock',
        'reorder_point',
        'stok_saat_ini',
        'status_stok',
        'rekomendasi',
        'tanggal_analisis',
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
            'tanggal_analisis' => 'date',
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
