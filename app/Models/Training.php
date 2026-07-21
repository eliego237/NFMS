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

        'created_at' => 'datetime',

        'updated_at' => 'datetime',

        'deleted_at' => 'datetime',

    ];

    /**
     * Les inscriptions liées à cette formation.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            Enrollment::class
        );
    }

    /**
     * Les modules de la formation.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(
            TrainingModule::class
        )->orderBy(
            'position'
        );
    }

    /**
     * Nombre de modules.
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
        return (int) $this->modules()->sum(
            'duration_hours'
        );
    }

    /**
     * Nombre d'étudiants inscrits.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    /**
     * Nombre d'inscriptions actives.
     */
    public function getActiveEnrollmentsCountAttribute(): int
    {
        return $this->enrollments()
            ->whereIn(
                'status',
                [
                    'pending',
                    'partial',
                ]
            )
            ->count();
    }

    /**
     * Montant total encaissé pour cette formation.
     */
    public function getRevenueAttribute(): float
    {
        return (float) $this->enrollments()
            ->sum(
                'amount_paid'
            );
    }

    /**
     * Scope des formations actives.
     */
    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }
}