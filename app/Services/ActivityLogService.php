<?php

namespace App\Services;

class ActivityLogService
{
    public static function log(
        string $module,
        string $event,
        $subject = null,
        array $properties = []
    ): void {

        $descriptions = [

            'login'   => 'Connexion',

            'logout'  => 'Déconnexion',

            'created' => 'Création',

            'updated' => 'Modification',

            'deleted' => 'Suppression',

            'printed' => 'Impression',

        ];

        activity($module)

            ->causedBy(auth()->user())

            ->performedOn($subject)

            ->event($event)

            ->withProperties(array_merge([

                'ip' => request()->ip(),

                'url' => request()->path(),

                'method' => request()->method(),

                'browser' => request()->userAgent(),

            ], $properties))

            ->log($descriptions[$event] ?? ucfirst($event));
    }
}