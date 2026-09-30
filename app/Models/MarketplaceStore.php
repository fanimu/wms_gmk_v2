<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MarketplaceStore
 *
 * @property int $id
 * @property string $platform
 * @property string $shop_id
 * @property string $shop_name
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property \Illuminate\Support\Carbon|null $token_expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_sync_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $platform_label
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MarketplaceProduct[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class MarketplaceStore extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marketplace_stores';

    protected $fillable = [
        'platform',
        'shop_id',
        'shop_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_active',
        'last_sync_at',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'last_sync_at' => 'datetime',
            'is_active' => 'boolean',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
    }

    /**
     * Relasi ke produk marketplace
     */
    public function products(): HasMany
    {
        return $this->hasMany(MarketplaceProduct::class, 'store_id');
    }

    /**
     * Relasi ke orders marketplace
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'store_id');
    }

    /**
     * Scope untuk store aktif
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan platform
     */
    public function scopeByPlatform(Builder $query, string $platform): void
    {
        $query->where('platform', $platform);
    }

    /**
     * Mengecek apakah token sudah kedaluwarsa
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->token_expires_at);
    }

    /**
     * Accessor untuk label platform dengan huruf kapital
     */
    public function getPlatformLabelAttribute(): string
    {
        return ucfirst($this->platform);
    }
}
