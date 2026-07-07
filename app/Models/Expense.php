<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'expense_number',

        'category',

        'title',

        'description',

        'amount',

        'payment_method_id',

        'expense_date',

        'reference',

        'recorded_by',

        'notes',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'amount' => 'decimal:2',

        'expense_date' => 'date',

    ];

    /**
     * Moyen de paiement utilisé.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Utilisateur ayant enregistré la dépense.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}