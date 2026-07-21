<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'matricule',

        'first_name',

        'last_name',

        'gender',

        'birth_date',

        'phone',

        'email',

        'address',

        'emergency_contact',

        'photo',

        'status',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'birth_date' => 'date',

        'status' => 'boolean',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',

        'deleted_at' => 'datetime',

    ];

    /**
     * Les inscriptions de l'étudiant.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            Enrollment::class
        );
    }

    /**
     * Dernière inscription.
     */
    public function latestEnrollment(): HasOne
    {
        return $this->hasOne(
            Enrollment::class
        )->latestOfMany();
    }

    /**
     * Nom complet de l'étudiant.
     */
    public function getFullNameAttribute(): string
    {
        return trim(

            $this->first_name . ' ' . $this->last_name

        );
    }

    /**
     * Vérifie si l'étudiant possède une inscription active.
     */
    public function hasActiveEnrollment(): bool
    {
        return $this->enrollments()
            ->whereIn(
                'status',
                [
                    'pending',
                    'partial',
                ]
            )
            ->exists();
    }

    /**
     * Scope des étudiants actifs.
     */
    public function scopeActive(Builder $query): Builder
{
    return $query->where('status', true);
}
}