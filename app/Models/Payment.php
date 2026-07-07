<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'enrollment_id',

        'receipt_number',

        'amount',

        'payment_method_id',

        'payment_date',

        'reference',

        'notes',

        'received_by',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'amount' => 'decimal:2',

        'payment_date' => 'date',

    ];

    /**
     * Inscription concernée.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Moyen de paiement utilisé.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Utilisateur ayant enregistré le paiement.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Étudiant concerné.
     */
    public function student(): BelongsTo
    {
        return $this->enrollment->student();
    }

    /**
     * Formation concernée.
     */
    public function training(): BelongsTo
    {
        return $this->enrollment->training();
    }
}