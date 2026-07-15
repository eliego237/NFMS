<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {

        static::created(function ($model) {

            ActivityLogService::log(

                class_basename($model),

                'Création',

                $model

            );

        });

        static::updated(function ($model) {

            ActivityLogService::log(

                class_basename($model),

                'Modification',

                $model

            );

        });

        static::deleted(function ($model) {

            ActivityLogService::log(

                class_basename($model),

                'Suppression',

                $model

            );

        });

    }
}