<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashTransactionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [

            'type' => 'sometimes|required|in:Entrée,Sortie',

            'category' => 'sometimes|required|string|max:100',

            'amount' => 'sometimes|required|numeric|min:0.01',

            'payment_method_id' => 'nullable|exists:payment_methods,id',

            'payment_id' => 'nullable|exists:payments,id',

            'description' => 'sometimes|required|string|max:255',

            'transaction_date' => 'sometimes|required|date',

            'notes' => 'nullable|string',

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'type.in' => 'Le type doit être Entrée ou Sortie.',

            'amount.numeric' => 'Le montant doit être numérique.',

            'amount.min' => 'Le montant doit être supérieur à zéro.',

        ];
    }
}