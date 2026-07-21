<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * Supprimer le cache du Dashboard.
     */
    public static function clear(): void
    {
        Cache::forget('dashboard.statistics');
    }
}