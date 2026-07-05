<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}