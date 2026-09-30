<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ActivityLog
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $module
 * @property string|null $description
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array|null $old_data
 * @property array|null $new_data
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Model|null $model
 */
class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
        ];
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation ke model yang berhubungan
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope berdasarkan module
     */
    public function scopeByModule(Builder $query, string $module): void
    {
        $query->where('module', $module);
    }

    /**
     * Scope berdasarkan action
     */
    public function scopeByAction(Builder $query, string $action): void
    {
        $query->where('action', $action);
    }

    /**
     * Scope berdasarkan user
     */
    public function scopeByUser(Builder $query, $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope untuk data terbaru berdasarkan jumlah hari
     */
    public function scopeRecent(Builder $query, int $days = 7): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Helper static untuk mencatat log aktivitas
     *
     * @param string $action
     * @param string $module
     * @param string|null $description
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param array|null $oldData
     * @param array|null $newData
     * @return self
     */
    public static function log(string $action, string $module, ?string $description = null, ?Model $model = null, ?array $oldData = null, ?array $newData = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
