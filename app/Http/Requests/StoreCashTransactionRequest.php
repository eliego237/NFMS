<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashTransactionRequest extends FormRequest
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

            'type' => 'required|in:Entrée,Sortie',

            'category' => 'required|string|max:100',

            'amount' => 'required|numeric|min:0.01',

            'payment_method_id' => 'nullable|exists:payment_methods,id',

            'payment_id' => 'nullable|exists:payments,id',

            'description' => 'required|string|max:255',

            'transaction_date' => 'required|date',

            'notes' => 'nullable|string',

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'type.required' => 'Le type de transaction est obligatoire.',

            'type.in' => 'Le type doit être Entrée ou Sortie.',

            'category.required' => 'La catégorie est obligatoire.',

            'amount.required' => 'Le montant est obligatoire.',

            'amount.numeric' => 'Le montant doit être numérique.',

            'amount.min' => 'Le montant doit être supérieur à zéro.',

            'description.required' => 'La description est obligatoire.',

            'transaction_date.required' => 'La date de transaction est obligatoire.',

        ];
    }
}