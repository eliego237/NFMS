<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * Table utilisée.
     */
    protected $table = 'activity_log';

    /**
     * Attributs assignables.
     */
    protected $fillable = [
        'log_name',
        'description',
        'event',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'attribute_changes',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'properties' => 'array',
        'attribute_changes' => 'array',
    ];

    /**
     * Utilisateur ayant effectué l'action.
     */
    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * Modèle concerné par l'action.
     */
    public function subject()
    {
        return $this->morphTo();
    }
}