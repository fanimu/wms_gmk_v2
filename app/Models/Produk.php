<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Produk
 *
 * @property int $id
 * @property string $sku
 * @property string $nama_produk
 * @property int $kategori_id
 * @property int $gudang_id
 * @property string|null $gender
 * @property string|null $merk
 * @property string|null $warna
 * @property string|null $ukuran
 * @property int|null $urutan_ukuran
 * @property string|null $grup_produk
 * @property float $harga_beli
 * @property float $harga_jual
 * @property int $stok
 * @property int $stok_minimum
 * @property string|null $gambar
 * @property string|null $deskripsi
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $is_stok_rendah
 * @property-read float $margin
 * @property-read \App\Models\Kategori $kategori
 * @property-read \App\Models\Gudang $gudang
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TransaksiDetail[] $transaksiDetail
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MarketplaceProduct[] $marketplaceProducts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $orderItems
 */
class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produk';

    protected $fillable = [
        'sku',
        'nama_produk',
        'kategori_id',
        'gudang_id',
        'gender',
        'merk',
        'warna',
        'ukuran',
        'urutan_ukuran',
        'grup_produk',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'gambar',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke Kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi ke Gudang
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    /**
     * Relasi ke Transaksi Detail
     */
    public function transaksiDetail(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    /**
     * Relasi ke Marketplace Product
     */
    public function marketplaceProducts(): HasMany
    {
        return $this->hasMany(MarketplaceProduct::class);
    }

    /**
     * Relasi ke Order Item
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope untuk produk aktif
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope untuk pencarian produk
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nama_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Scope untuk stok rendah (dibawah atau sama dengan stok minimum)
     */
    public function scopeStokRendah(Builder $query): void
    {
        $query->whereColumn('stok', '<=', 'stok_minimum')
              ->where('stok_minimum', '>', 0);
    }

    /**
     * Scope filter berdasarkan gudang
     */
    public function scopeByGudang(Builder $query, $gudangId): void
    {
        $query->where('gudang_id', $gudangId);
    }

    /**
     * Scope filter berdasarkan kategori
     */
    public function scopeByKategori(Builder $query, $kategoriId): void
    {
        $query->where('kategori_id', $kategoriId);
    }

    /**
     * Accessor untuk mengecek apakah stok rendah
     */
    public function getIsStokRendahAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum && $this->stok_minimum > 0;
    }

    /**
     * Accessor untuk menghitung margin keuntungan
     */
    public function getMarginAttribute(): float
    {
        return (float) ($this->harga_jual - $this->harga_beli);
    }
}
