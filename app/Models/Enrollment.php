<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'enrollment_number',

        'student_id',

        'training_id',

        'registration_fee',

        'training_fee',

        'discount',

        'total_amount',

        'amount_paid',

        'balance',

        'status',

        'academic_year',

        'enrolled_at',

        'created_by',

        'notes',

    ];

    protected $casts = [

        'registration_fee' => 'decimal:2',

        'training_fee'     => 'decimal:2',

        'discount'         => 'decimal:2',

        'total_amount'     => 'decimal:2',

        'amount_paid'      => 'decimal:2',

        'balance'          => 'decimal:2',

        'enrolled_at'      => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Métier
    |--------------------------------------------------------------------------
    */

    /**
     * L'inscription est entièrement soldée.
     */
    public function isFullyPaid(): bool
    {
        return $this->balance <= 0;
    }

    /**
     * Paiement partiel.
     */
    public function isPartial(): bool
    {
        return $this->amount_paid > 0 && $this->balance > 0;
    }

    /**
     * Il reste un solde.
     */
    public function hasBalance(): bool
    {
        return $this->balance > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Progression du paiement (%)
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        return round(
            ($this->amount_paid / $this->total_amount) * 100,
            2
        );
    }

    /**
     * Montant réellement économisé grâce à la remise.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return number_format(
            $this->discount,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }

    /**
     * Montant total formaté.
     */
    public function getFormattedRegistrationFeeAttribute(): string
    {
    return number_format(
        $this->registration_fee,
        0,
        ',',
        ' '
    ) . ' FCFA';
    }

    public function getFormattedTrainingFeeAttribute(): string
    {
    return number_format(
        $this->training_fee,
        0,
        ',',
        ' '
    ) . ' FCFA';
    }
    public function getFormattedTotalAmountAttribute(): string
    {
    return number_format(
        $this->total_amount,
        0,
        ',',
        ' '
    ) . ' FCFA';
    }
    /**
     * Montant déjà payé formaté.
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return number_format(
            $this->amount_paid,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }

    /**
     * Solde formaté.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format(
            $this->balance,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }

    /**
     * Libellé français du statut.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {

            'pending'   => 'En attente',

            'partial'   => 'Partiellement payé',

            'paid'      => 'Soldé',

            'cancelled' => 'Annulé',

            default     => $this->status,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Calculs
    |--------------------------------------------------------------------------
    */

    /**
     * Recalcul du solde et du statut.
     */
    public function refreshBalance(): void
    {
        $this->balance = max(
            0,
            $this->total_amount - $this->amount_paid
        );

        if ($this->balance == 0) {

            $this->status = 'paid';

        } elseif ($this->amount_paid > 0) {

            $this->status = 'partial';

        } else {

            $this->status = 'pending';

        }

        $this->save();
    }
}