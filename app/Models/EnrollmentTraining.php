<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentTraining extends Model
{
    protected $fillable = [
        'enrollment_id',
        'training_id',
        'training_fee',
        'duration_months',
    ];

    protected $casts = [
        'training_fee' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    /**
     * Inscription à laquelle appartient cette formation.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(
            Enrollment::class
        );
    }

    /**
     * Formation concernée.
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(
            Training::class
        );
    }
}