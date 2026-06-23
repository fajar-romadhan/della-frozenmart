<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockNotification - Notifikasi stok untuk pengguna.
 *
 * Dinamai StockNotification untuk menghindari konflik dengan
 * Illuminate\Notifications\Notification bawaan Laravel.
 *
 * Jenis notifikasi: stok warning, stok order, import berhasil,
 * produk mendekati kedaluwarsa, dll.
 */
class StockNotification extends Model
{
    use HasFactory;

    /**
     * Nama tabel database.
     */
    protected $table = 'notifikasi';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'judul',
        'pesan',
        'status_baca',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_baca' => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
