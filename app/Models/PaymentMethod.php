<?php

namespace App\Models;

use LogsActivity;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use SoftDeletes;

    /**
     * Les attributs pouvant être remplis.
     */
    protected $fillable = [

        'name',

        'code',

        'is_active',

    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [

        'is_active' => 'boolean',

    ];

    /**
     * Les paiements utilisant ce moyen de paiement.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Les dépenses utilisant ce moyen de paiement.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Nombre total d'utilisations.
     */
    public function getUsageCountAttribute(): int
    {
        return $this->payments()->count()
            + $this->expenses()->count();
    }
}