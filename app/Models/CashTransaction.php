<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use SoftDeletes;

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

    /**
     * Paiement à l'origine de l'opération.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Dépense à l'origine de l'opération.
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
     * Utilisateur ayant enregistré l'opération.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}