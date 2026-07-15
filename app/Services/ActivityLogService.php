<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function log(
        string $module,
        string $event,
        $model
    ): void {

        ActivityLog::create([

            'log_name' => $module,

            'description' => $event,

            'event' => strtolower($event),

            'subject_type' => get_class($model),

            'subject_id' => $model->id,

            'causer_type' => auth()->check()
                ? get_class(auth()->user())
                : null,

            'causer_id' => auth()->id(),

            'properties' => $model->toArray(),

            'attribute_changes' => [],

        ]);

    }
}