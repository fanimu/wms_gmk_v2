<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $produk_id
 * @property string $product_name
 * @property string|null $sku
 * @property int $quantity
 * @property float $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $subtotal
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Produk|null $produk
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'produk_id',
        'product_name',
        'sku',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Produk
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Accessor untuk menghitung subtotal
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->quantity * $this->price);
    }
}
