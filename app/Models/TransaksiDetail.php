<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TransaksiDetail
 *
 * @property int $id
 * @property int $transaksi_id
 * @property int $produk_id
 * @property int $qty
 * @property float $harga_satuan
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $subtotal
 * @property-read \App\Models\Transaksi $transaksi
 * @property-read \App\Models\Produk $produk
 */
class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'qty',
        'harga_satuan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Transaksi
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    /**
     * Relasi ke Produk
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Accessor untuk subtotal (qty * harga_satuan)
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->qty * $this->harga_satuan);
    }
}
