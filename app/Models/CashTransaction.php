<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CashTransaction extends Model
{
    use SoftDeletes;

    /**
     * Types de transaction.
     */
    public const TYPE_INCOME = 'Entrée';

    public const TYPE_EXPENSE = 'Sortie';

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'transaction_number',

        'type',

        'category',

        'amount',

        'payment_method_id',

        'payment_id',

        'expense_id',

        'description',

        'transaction_date',

        'recorded_by',

        'notes',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'amount' => 'decimal:2',

        'transaction_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    
    public function scopeIncome(Builder $query): Builder
{
    return $query->where('type', 'Entrée');
}

public function scopeExpense(Builder $query): Builder
{
    return $query->where('type', 'Sortie');
}

    /**
     * Paiement à l'origine de la transaction.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Dépense à l'origine de la transaction.
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Moyen de paiement utilisé.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Utilisateur ayant enregistré la transaction.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Transactions d'entrée.
     */
    public function scopeOnlyIncome($query)
    {
        return $query->where(
            'type',
            self::TYPE_INCOME
        );
    }

    /**
     * Transactions de sortie.
     */
    public function scopeOnlyExpenses($query)
    {
        return $query->where(
            'type',
            self::TYPE_EXPENSE
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Métier
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si la transaction est une entrée.
     */
    public function isIncome(): bool
    {
        return $this->type === self::TYPE_INCOME;
    }

    /**
     * Vérifie si la transaction est une sortie.
     */
    public function isExpense(): bool
    {
        return $this->type === self::TYPE_EXPENSE;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Type formaté.
     */
    public function getFormattedTypeAttribute(): string
    {
        return match ($this->type) {

            self::TYPE_INCOME => 'Entrée',

            self::TYPE_EXPENSE => 'Sortie',

            default => $this->type,

        };
    }

    /**
     * Montant formaté.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format(

            $this->amount,

            0,

            ',',

            ' '

        ) . ' FCFA';
    }
}