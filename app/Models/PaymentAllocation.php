<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $fillable = [
        'payment_id',
        'enrollment_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Paiement global.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

    /**
     * Inscription concernée.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(
            Enrollment::class
        );
    }
}