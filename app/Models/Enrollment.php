<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'enrollment_number',
        'student_id',
        'registration_fee',
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
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'enrolled_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(
            Training::class,
            'enrollment_trainings'
        )
        ->withPivot([
            'training_fee',
            'duration_months',
        ])
        ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopePartial(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_PARTIAL
        );
    }

    public function scopePaid(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_PAID
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            '!=',
            self::STATUS_CANCELLED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Financial calculations
    |--------------------------------------------------------------------------
    */

    /**
     * Total des frais d'inscription.
     *
     * Les frais d'inscription sont totalement séparés
     * des frais de formation.
     */
    public function getRegistrationTotalAttribute(): float
    {
        return max(
            0,
            (float) ($this->registration_fee ?? 0)
        );
    }

    /**
     * Montant déjà payé pour les frais d'inscription.
     */
    public function getRegistrationPaidAttribute(): float
    {
        if (! $this->relationLoaded('payments')) {
            return (float) $this->payments()
                ->where(
                    'payment_type',
                    Payment::TYPE_REGISTRATION
                )
                ->sum('amount');
        }

        return (float) $this->payments
            ->where(
                'payment_type',
                Payment::TYPE_REGISTRATION
            )
            ->sum('amount');
    }

    /**
     * Reste des frais d'inscription.
     */
    public function getRegistrationBalanceAttribute(): float
    {
        return max(
            0,
            $this->registration_total
            - $this->registration_paid
        );
    }

    /**
     * Montant total brut des formations.
     */
    public function getTrainingTotalAttribute(): float
    {
        if (! $this->relationLoaded('trainings')) {
            return (float) $this->trainings()
                ->get()
                ->sum(function ($training) {
                    return (float) (
                        $training->pivot->training_fee
                        ?? $training->price
                        ?? 0
                    );
                });
        }

        return (float) $this->trainings->sum(
            function ($training) {
                return (float) (
                    $training->pivot->training_fee
                    ?? $training->price
                    ?? 0
                );
            }
        );
    }

    /**
     * Remise appliquée uniquement aux formations.
     *
     * Les frais d'inscription ne sont jamais concernés
     * par cette remise.
     */
    public function getTrainingDiscountAttribute(): float
    {
        return min(
            $this->training_total,
            max(
                0,
                (float) ($this->discount ?? 0)
            )
        );
    }

    /**
     * Total net des formations après remise.
     */
    public function getTrainingNetTotalAttribute(): float
    {
        return max(
            0,
            $this->training_total
            - $this->training_discount
        );
    }

    /**
     * Montant déjà payé pour les formations.
     */
    public function getTrainingPaidAttribute(): float
    {
        if (! $this->relationLoaded('payments')) {
            return (float) $this->payments()
                ->where(
                    'payment_type',
                    Payment::TYPE_TRAINING
                )
                ->sum('amount');
        }

        return (float) $this->payments
            ->where(
                'payment_type',
                Payment::TYPE_TRAINING
            )
            ->sum('amount');
    }

    /**
     * Reste des frais de formation.
     */
    public function getTrainingBalanceAttribute(): float
    {
        return max(
            0,
            $this->training_net_total
            - $this->training_paid
        );
    }

    /**
     * Total net global.
     *
     * Inscription + formations après remise.
     */
    public function getNetTotalAttribute(): float
    {
        return max(
            0,
            $this->registration_total
            + $this->training_net_total
        );
    }

    /**
     * Montant total réellement payé.
     *
     * Inscription + formations.
     */
    public function getTotalPaidAttribute(): float
    {
        return max(
            0,
            $this->registration_paid
            + $this->training_paid
        );
    }

    /**
     * Solde global.
     *
     * Inscription + formations.
     */
    public function getGlobalBalanceAttribute(): float
    {
        return max(
            0,
            $this->registration_balance
            + $this->training_balance
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Balance
    |--------------------------------------------------------------------------
    */

    /**
     * Recalculer le solde global de l'inscription.
     *
     * IMPORTANT :
     *
     * registration_balance
     * et
     * training_balance
     *
     * sont calculés séparément.
     */
    public function refreshBalance(): self
    {
        /*
         * amount_paid reste une valeur globale
         * représentant toutes les sommes encaissées.
         */
        $this->amount_paid = $this->total_paid;

        /*
         * Le balance global est calculé séparément :
         *
         * inscription restante
         * +
         * formation restante.
         */
        $this->balance = $this->global_balance;

        /*
         * Déterminer automatiquement le statut.
         */
        if ($this->balance <= 0) {
            $this->status = self::STATUS_PAID;
        } elseif ($this->amount_paid > 0) {
            $this->status = self::STATUS_PARTIAL;
        } else {
            $this->status = self::STATUS_PENDING;
        }

        $this->save();

        return $this;
    }

    /**
     * Vérifier si l'inscription est entièrement payée.
     */
    public function isFullyPaid(): bool
    {
        return $this->global_balance <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsPaidAttribute(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getIsPartialAttribute(): bool
    {
        return $this->status === self::STATUS_PARTIAL;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getTrainingCountAttribute(): int
    {
        if ($this->relationLoaded('trainings')) {
            return $this->trainings->count();
        }

        return $this->trainings()->count();
    }

    public function getTrainingNamesAttribute(): string
    {
        if (! $this->relationLoaded('trainings')) {
            $this->load('trainings');
        }

        return $this->trainings
            ->pluck('title')
            ->filter()
            ->implode(' + ');
    }
}