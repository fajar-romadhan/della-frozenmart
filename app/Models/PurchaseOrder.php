<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PurchaseOrder - Pemesanan barang ke supplier.
 *
 * Status alur: draft → dipesan → diterima / dibatalkan.
 */
class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'pemesanan_supplier';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'supplier_id',
        'product_id',
        'jumlah_pesan',
        'tanggal_pemesanan',
        'status_pemesanan',
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
            'tanggal_pemesanan' => 'date',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
