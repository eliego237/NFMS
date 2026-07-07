<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use SoftDeletes;

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
     * Un moyen de paiement possède plusieurs paiements.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}