<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingModule extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'training_id',

        'code',

        'title',

        'description',

        'duration_hours',

        'position',

        'is_active',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

    'duration_hours' => 'integer',

    'position' => 'integer',

    'is_active' => 'boolean',

    'created_at' => 'datetime',

    'updated_at' => 'datetime',

    'deleted_at' => 'datetime',

];

    /**
     * Formation à laquelle appartient le module.
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}