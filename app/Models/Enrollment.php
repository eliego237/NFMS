<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
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

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'registration_fee' => 'decimal:2',

        'training_fee' => 'decimal:2',

        'discount' => 'decimal:2',

        'total_amount' => 'decimal:2',

        'amount_paid' => 'decimal:2',

        'balance' => 'decimal:2',

        'enrolled_at' => 'date',

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
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Paiements liés à cette inscription.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes métier
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'inscription est totalement soldée.
     */
    public function isFullyPaid(): bool
    {
        return $this->balance <= 0;
    }

    /**
     * Vérifie si un paiement partiel a été effectué.
     */
    public function isPartial(): bool
    {
        return $this->amount_paid > 0 && $this->balance > 0;
    }

    /**
     * Vérifie s'il reste un montant à payer.
     */
    public function hasBalance(): bool
    {
        return $this->balance > 0;
    }

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
     * Statut calculé selon le paiement.
     */
    public function getFormattedStatusAttribute(): string
    {
        if ($this->isFullyPaid()) {
            return 'Soldé';
        }

        if ($this->isPartial()) {
            return 'Partiellement payé';
        }

        return 'En attente';
    }

    /**
     * Recalcule automatiquement le solde.
     */
    public function refreshBalance(): void
    {
        $this->balance = max(
            0,
            $this->total_amount - $this->amount_paid
        );

        $this->status = $this->getFormattedStatusAttribute();

        $this->save();
    }
}