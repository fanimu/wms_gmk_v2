<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public function log(string $action, string $module, ?string $description = null, ?Model $model = null, ?array $oldData = null, ?array $newData = null): ActivityLog
    {
        return ActivityLog::create([
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

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 20): Collection
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by module
     */
    public function getActivitiesByModule(string $module, int $limit = 50): Collection
    {
        return ActivityLog::with('user')
            ->where('module', $module)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by user
     */
    public function getActivitiesByUser(int $userId, int $limit = 50): Collection
    {
        return ActivityLog::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
