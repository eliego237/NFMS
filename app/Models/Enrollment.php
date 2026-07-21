<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Statuts
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Attributs
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'registration_fee' => 'decimal:2',

        'training_fee' => 'decimal:2',

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

    /**
     * Étudiant inscrit.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Formation choisie.
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * Utilisateur ayant créé l'inscription.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Paiements liés à l'inscription.
     */
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
     * Vérifie si l'inscription est entièrement payée.
     */
    public function isFullyPaid(): bool
    {
        return $this->status === static::STATUS_PAID;
    }

    /**
     * Vérifie si l'inscription est partiellement payée.
     */
    public function isPartial(): bool
    {
        return $this->status === static::STATUS_PARTIAL;
    }

    /**
     * Vérifie s'il reste un solde.
     */
    public function hasBalance(): bool
    {
        return $this->balance > 0;
    }

    /**
     * Recalcule automatiquement le solde et le statut.
     */
    public function refreshBalance(): void
    {
        $this->balance = max(
            0,
            $this->total_amount - $this->amount_paid
        );

        if ($this->balance == 0) {

            $this->status = static::STATUS_PAID;

        } elseif ($this->amount_paid > 0) {

            $this->status = static::STATUS_PARTIAL;

        } else {

            $this->status = static::STATUS_PENDING;

        }

        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Pourcentage de paiement.
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
     * Frais d'inscription formatés.
     */
    public function getFormattedRegistrationFeeAttribute(): string
    {
        return number_format($this->registration_fee, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Frais de formation formatés.
     */
    public function getFormattedTrainingFeeAttribute(): string
    {
        return number_format($this->training_fee, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Remise formatée.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Montant total formaté.
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Montant payé formaté.
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return number_format($this->amount_paid, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Solde formaté.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Libellé du statut.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {

            static::STATUS_PENDING => 'En attente',

            static::STATUS_PARTIAL => 'Partiellement payé',

            static::STATUS_PAID => 'Soldé',

            static::STATUS_CANCELLED => 'Annulé',

            default => $this->status,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->where(
            'status',
            static::STATUS_PENDING
        );
    }

    public function scopePartial(Builder $query): Builder
    {
        return $query->where(
            'status',
            static::STATUS_PARTIAL
        );
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where(
            'status',
            static::STATUS_PAID
        );
    }

    public function scopeWithBalance(Builder $query): Builder
    {
        return $query->where(
            'balance',
            '>',
            0
        );
    }
}