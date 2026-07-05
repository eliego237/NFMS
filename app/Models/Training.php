<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'duration_months',
        'is_active',
    ];

    /**
     * Une formation possède plusieurs inscriptions.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}