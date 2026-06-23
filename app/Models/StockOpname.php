<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockOpname - Pencocokan stok sistem vs fisik.
 *
 * Digunakan untuk audit berkala guna memastikan akurasi data persediaan.
 */
class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'tanggal_opname',
        'keterangan',
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
            'tanggal_opname' => 'date',
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
