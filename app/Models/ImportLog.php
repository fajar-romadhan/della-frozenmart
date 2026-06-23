<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ImportLog - Log import file Excel.
 *
 * Mencatat hasil import faktur pembelian dan data penjualan
 * termasuk jumlah baris berhasil/gagal dan catatan error.
 */
class ImportLog extends Model
{
    use HasFactory;

    protected $table = 'log_import';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jenis_import',
        'nama_file',
        'jumlah_baris',
        'jumlah_berhasil',
        'jumlah_gagal',
        'catatan_error',
        'user_id',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
