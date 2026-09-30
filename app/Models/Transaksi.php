<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Transaksi
 *
 * @property int $id
 * @property string $kode_transaksi
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $jenis
 * @property int|null $supplier_id
 * @property int $gudang_id
 * @property int $user_id
 * @property string|null $keterangan
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property int|null $confirmed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int $total_items
 * @property-read float $total_nilai
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\Gudang $gudang
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $confirmedByUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TransaksiDetail[] $detail
 */
class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'jenis',
        'supplier_id',
        'gudang_id',
        'user_id',
        'keterangan',
        'status',
        'confirmed_at',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke Gudang
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    /**
     * Relasi ke User pembuat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke User pengonfirmasi
     */
    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Relasi ke Transaksi Detail
     */
    public function detail(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    /**
     * Scope untuk pencarian transaksi
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Scope berdasarkan jenis transaksi
     */
    public function scopeByJenis(Builder $query, string $jenis): void
    {
        $query->where('jenis', $jenis);
    }

    /**
     * Scope berdasarkan status transaksi
     */
    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope berdasarkan periode tanggal
     */
    public function scopeByPeriode(Builder $query, $dari, $sampai): void
    {
        $query->whereBetween('tanggal', [$dari, $sampai]);
    }

    /**
     * Accessor untuk total kuantitas barang
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->detail->sum('qty');
    }

    /**
     * Accessor untuk total nilai transaksi (qty * harga satuan)
     */
    public function getTotalNilaiAttribute(): float
    {
        return $this->detail->sum(function ($item) {
            return $item->qty * $item->harga_satuan;
        });
    }

    /**
     * Cek apakah status draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Cek apakah status confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Cek apakah status cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
