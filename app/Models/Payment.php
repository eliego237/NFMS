<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'enrollment_id',
        'receipt_number',
        'payment_type',
        'amount',
        'payment_method_id',
        'payment_date',
        'reference',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Types de paiement.
     */
    public const TYPE_REGISTRATION = 'registration';

    public const TYPE_TRAINING = 'training';

    /**
     * Inscription concernée.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Allocations du paiement.
     *
     * Permet à un paiement d'être réparti
     * sur plusieurs inscriptions si nécessaire.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
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
        return $this->belongsTo(
            User::class,
            'received_by'
        );
    }

    /**
     * Transaction de caisse liée au paiement.
     */
    public function cashTransaction(): HasOne
    {
        return $this->hasOne(
            CashTransaction::class,
            'payment_id'
        );
    }

    /**
     * Vérifie s'il s'agit d'un paiement d'inscription.
     */
    public function isRegistration(): bool
    {
        return $this->payment_type === self::TYPE_REGISTRATION;
    }

    /**
     * Vérifie s'il s'agit d'un paiement de formation.
     */
    public function isTraining(): bool
    {
        return $this->payment_type === self::TYPE_TRAINING;
    }

    /**
     * Libellé du type de paiement.
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::TYPE_REGISTRATION => 'Frais d’inscription',
            self::TYPE_TRAINING => 'Paiement formation',
            default => 'Paiement',
        };
    }
}