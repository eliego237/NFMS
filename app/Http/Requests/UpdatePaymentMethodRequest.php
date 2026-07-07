<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods', 'name')->ignore($this->paymentMethod),
            ],

            'is_active' => 'nullable|boolean',

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Le nom du moyen de paiement est obligatoire.',

            'name.unique' => 'Ce moyen de paiement existe déjà.',

            'name.max' => 'Le nom ne doit pas dépasser 100 caractères.',

            'is_active.boolean' => 'Le statut est invalide.',

        ];
    }
}