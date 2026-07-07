<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'code',

        'title',

        'category',

        'description',

        'price',

        'duration_months',

        'certificate',

        'is_active',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'price' => 'decimal:2',

        'duration_months' => 'integer',

        'is_active' => 'boolean',

    ];

    /**
     * Une formation possède plusieurs inscriptions.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Une formation possède plusieurs modules.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(TrainingModule::class)
                    ->orderBy('position');
    }

    /**
     * Nombre total de modules.
     */
    public function getModulesCountAttribute(): int
    {
        return $this->modules()->count();
    }

    /**
     * Volume horaire total.
     */
    public function getTotalHoursAttribute(): int
    {
        return (int) $this->modules()->sum('duration_hours');
    }
}