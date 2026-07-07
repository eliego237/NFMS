<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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

            'enrollment_id' => 'required|exists:enrollments,id',

            'amount' => 'required|numeric|min:1',

            'payment_method_id' => 'required|exists:payment_methods,id',

            'payment_date' => 'required|date',

            'reference' => 'nullable|string|max:255',

            'notes' => 'nullable|string',

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'enrollment_id.required' => 'Veuillez sélectionner une inscription.',
            'enrollment_id.exists' => 'Cette inscription est introuvable.',

            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à zéro.',

            'payment_method_id.required' => 'Veuillez choisir un moyen de paiement.',
            'payment_method_id.exists' => 'Le moyen de paiement sélectionné est invalide.',

            'payment_date.required' => 'La date de paiement est obligatoire.',
            'payment_date.date' => 'La date de paiement est invalide.',
        ];
    }
}