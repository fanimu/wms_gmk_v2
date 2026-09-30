<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ActivityLogService;

class LogActivity
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya log request GET yang sukses dan bukan request Ajax/Livewire
        if ($request->isMethod('GET') && $response->isSuccessful() && !$request->ajax() && !$request->header('X-Livewire')) {
            $routeName = $request->route() ? $request->route()->getName() : $request->path();
            $module = $routeName ?? 'unknown';

            $this->activityLog->log(
                'page_access',
                $module,
                "Mengakses halaman {$routeName}"
            );
        }

        return $response;
    }
}
