<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [

        'key',

        'value',

        'description',

    ];

    /**
     * Retourne la valeur d'un paramètre.
     */
    public static function getValue(string $key, $default = null)
    {
        return static::where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Enregistre ou met à jour un paramètre.
     */
    public static function setValue(
        string $key,
        $value,
        ?string $description = null
    ): void {

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
            ]
        );

    }
}