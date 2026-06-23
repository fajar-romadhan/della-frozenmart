<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
        'tanggal_kedaluwarsa',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kedaluwarsa' => 'date',
            'status_aktif' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function incomingGoods(): HasMany
    {
        return $this->hasMany(IncomingGood::class);
    }

    public function outgoingGoods(): HasMany
    {
        return $this->hasMany(OutgoingGood::class);
    }

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class);
    }

    public function inventoryAnalyses(): HasMany
    {
        return $this->hasMany(InventoryAnalysis::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function latestAnalysis()
    {
        return $this->hasOne(InventoryAnalysis::class)->latestOfMany();
    }

    public function latestIncomingGood()
    {
        return $this->hasOne(IncomingGood::class)->latestOfMany('tanggal_masuk');
    }

    public function availableBatches()
    {
        return $this->stockBatches()
            ->where('jumlah_sisa', '>', 0)
            ->orderByRaw('CASE WHEN tanggal_kedaluwarsa IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tanggal_kedaluwarsa', 'asc')
            ->orderBy('tanggal_masuk', 'asc');
    }

    public function getShortCodeAttribute()
    {
        $name = $this->nama_produk;
        preg_match('/(\d+)\s*(Gr|Pack|Kg|ML|Pcs|Ltr)/i', $name, $matches);
        $suffix = '';
        if ($matches) {
            $suffix = '-' . strtoupper($matches[1]) . strtoupper(substr($matches[2] ?? '', 0, 3));
        } else {
            preg_match('/(\d+)/', $name, $matches);
            if ($matches) {
                $suffix = '-' . $matches[1];
            }
        }
        
        $cleanName = preg_replace('/[\(\)\[\]\-\d]+\s*(Gr|Pack|Kg|ML|Pcs|Ltr|is\s*\d+)?/i', '', $name);
        $cleanName = preg_replace('/\s+/', ' ', $cleanName);
        
        $words = explode(' ', trim($cleanName));
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                if (in_array(strtolower($word), ['dan', 'atau', 'isi']) && count($words) > 2) {
                    continue;
                }
                $initials .= strtoupper($word[0]);
            }
        }
        
        if (empty($initials)) {
            $initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 4));
        }
        
        $initials = substr($initials, 0, 4);
        return $initials . $suffix;
    }

    public static function generateKodeProduk(): string
    {
        $lastProduct = self::orderByRaw("CAST(SUBSTRING(kode_produk, 5) AS UNSIGNED) DESC")->first();

        if ($lastProduct && preg_match('/PRD-(\d+)/', $lastProduct->kode_produk, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'PRD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
