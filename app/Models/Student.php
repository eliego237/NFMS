<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}