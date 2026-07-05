<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [

        'student_id',

        'training_id',

        'registration_number',

        'registration_date',

        'status',

        'notes',

    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}