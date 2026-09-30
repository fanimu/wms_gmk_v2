<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MarketplaceProduct
 *
 * @property int $id
 * @property int $produk_id
 * @property int $store_id
 * @property string $platform_product_id
 * @property float|null $platform_price
 * @property int|null $platform_stock
 * @property string $sync_status
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property string|null $sync_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Produk $produk
 * @property-read \App\Models\MarketplaceStore $store
 */
class MarketplaceProduct extends Model
{
    use HasFactory;

    protected $table = 'marketplace_products';

    protected $fillable = [
        'produk_id',
        'store_id',
        'platform_product_id',
        'platform_price',
        'platform_stock',
        'sync_status',
        'last_synced_at',
        'sync_error',
    ];

    protected function casts(): array
    {
        return [
            'platform_price' => 'decimal:2',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Produk
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Relasi ke MarketplaceStore
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(MarketplaceStore::class, 'store_id');
    }

    /**
     * Scope berdasarkan status sinkronisasi
     */
    public function scopeBySyncStatus(Builder $query, string $status): void
    {
        $query->where('sync_status', $status);
    }

    /**
     * Scope untuk data yang butuh sinkronisasi
     */
    public function scopeNeedSync(Builder $query): void
    {
        $query->where('sync_status', '!=', 'synced');
    }
}
