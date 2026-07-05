<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use SoftDeletes;

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
        'formation',
        'registration_date',
        'photo',
        'status',
    ];

    /**
     * Les inscriptions de l'étudiant.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}