<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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

            'category' => 'sometimes|required|string|max:100',

            'title' => 'sometimes|required|string|max:255',

            'description' => 'nullable|string',

            'amount' => 'sometimes|required|numeric|min:0.01',

            'payment_method_id' => 'nullable|exists:payment_methods,id',

            'expense_date' => 'sometimes|required|date',

            'reference' => 'nullable|string|max:100',

            'notes' => 'nullable|string',

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'category.required' => 'La catégorie est obligatoire.',

            'title.required' => 'Le libellé de la dépense est obligatoire.',

            'amount.required' => 'Le montant est obligatoire.',

            'amount.numeric' => 'Le montant doit être numérique.',

            'amount.min' => 'Le montant doit être supérieur à zéro.',

            'expense_date.required' => 'La date de la dépense est obligatoire.',

        ];
    }
}