<?php

namespace App\Models;

use LogsActivity;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Transaction de caisse générée par cette dépense.
     */
    public function cashTransaction(): HasOne
    {
        return $this->hasOne(CashTransaction::class);
    }

    /**
     * Vérifie si la dépense est importante.
     */
    public function isLargeExpense(float $threshold = 100000): bool
    {
        return $this->amount >= $threshold;
    }
}