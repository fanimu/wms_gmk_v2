<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Gudang
 *
 * @property int $id
 * @property string $kode_gudang
 * @property string $nama_gudang
 * @property string|null $lokasi
 * @property string|null $penanggung_jawab
 * @property string|null $telepon
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Produk[] $produk
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaksi[] $transaksi
 */
class Gudang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gudang';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'lokasi',
        'penanggung_jawab',
        'telepon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot method untuk auto-generate kode gudang
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_gudang)) {
                // Mendapatkan gudang terakhir untuk penomoran
                $lastGudang = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastGudang ? $lastGudang->id + 1 : 1;
                $model->kode_gudang = 'GDG-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Produk
     */
    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    /**
     * Relasi ke Transaksi
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Scope untuk gudang aktif
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope untuk pencarian gudang
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_gudang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('kode_gudang', 'like', "%{$search}%");
            });
        }
    }
}
