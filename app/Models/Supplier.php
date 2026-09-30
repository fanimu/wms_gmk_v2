<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Supplier
 *
 * @property int $id
 * @property string $kode_supplier
 * @property string $nama_supplier
 * @property string|null $kontak_person
 * @property string|null $telepon
 * @property string|null $email
 * @property string|null $alamat
 * @property string|null $kota
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaksi[] $transaksi
 */
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'kontak_person',
        'telepon',
        'email',
        'alamat',
        'kota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot method untuk auto-generate kode supplier
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_supplier)) {
                $lastSupplier = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastSupplier ? $lastSupplier->id + 1 : 1;
                $model->kode_supplier = 'SUP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Transaksi
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Scope untuk supplier aktif
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope untuk pencarian supplier
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%")
                  ->orWhere('kontak_person', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }
    }
}
