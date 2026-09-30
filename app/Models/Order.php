<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Order
 *
 * @property int $id
 * @property string $order_number
 * @property int $store_id
 * @property string $platform
 * @property string|null $platform_order_id
 * @property string $status
 * @property float $total_amount
 * @property float $shipping_fee
 * @property string $buyer_name
 * @property string|null $buyer_phone
 * @property string|null $buyer_address
 * @property string|null $tracking_number
 * @property string|null $shipping_provider
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read float $grand_total
 * @property-read int $total_items
 * @property-read \App\Models\MarketplaceStore $store
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'store_id',
        'platform',
        'platform_order_id',
        'status',
        'total_amount',
        'shipping_fee',
        'buyer_name',
        'buyer_phone',
        'buyer_address',
        'tracking_number',
        'shipping_provider',
        'notes',
        'order_date',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'order_date' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke MarketplaceStore
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(MarketplaceStore::class, 'store_id');
    }

    /**
     * Relasi ke Order Item
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope pencarian pesanan
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('platform_order_id', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Scope berdasarkan platform
     */
    public function scopeByPlatform(Builder $query, string $platform): void
    {
        $query->where('platform', $platform);
    }

    /**
     * Scope berdasarkan status
     */
    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope berdasarkan periode order_date
     */
    public function scopeByPeriode(Builder $query, $dari, $sampai): void
    {
        $query->whereBetween('order_date', [$dari, $sampai]);
    }

    /**
     * Accessor untuk grand total (total amount + shipping fee)
     */
    public function getGrandTotalAttribute(): float
    {
        return (float) ($this->total_amount + $this->shipping_fee);
    }

    /**
     * Accessor untuk total kuantitas item dalam pesanan
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
