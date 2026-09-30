<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Kategori
 *
 * @property int $id
 * @property string $nama_kategori
 * @property string|null $sub_kategori
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $full_kategori
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Produk[] $produk
 */
class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'sub_kategori',
        'deskripsi',
    ];

    /**
     * Relasi ke Produk
     */
    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    /**
     * Scope untuk pencarian kategori
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('sub_kategori', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Accessor untuk mendapatkan nama kategori lengkap dengan sub kategori
     */
    public function getFullKategoriAttribute(): string
    {
        if ($this->sub_kategori) {
            return $this->nama_kategori . ' - ' . $this->sub_kategori;
        }
        
        return $this->nama_kategori;
    }
}
