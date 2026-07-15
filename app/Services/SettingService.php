<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Retourner tous les paramètres.
     */
    public static function all(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Mettre à jour les paramètres.
     */
    public static function update(array $data): void
    {
        foreach ($data as $key => $value) {

            Setting::setValue($key, $value);

        }
    }
}