<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Autorisation.
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

            'enrollment_id' => [
                'required',
                'exists:enrollments,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'payment_method_id' => [
                'required',
                'exists:payment_methods,id',
            ],

            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'enrollment_id.required' =>
                'Veuillez sélectionner une inscription.',

            'enrollment_id.exists' =>
                'Cette inscription est introuvable.',

            'amount.required' =>
                'Le montant est obligatoire.',

            'amount.numeric' =>
                'Le montant doit être numérique.',

            'amount.min' =>
                'Le montant doit être supérieur à zéro.',

            'payment_method_id.required' =>
                'Veuillez choisir un moyen de paiement.',

            'payment_method_id.exists' =>
                'Le moyen de paiement sélectionné est invalide.',

            'payment_date.required' =>
                'La date de paiement est obligatoire.',

            'payment_date.date' =>
                'La date de paiement est invalide.',

            'payment_date.before_or_equal' =>
                'La date de paiement ne peut pas être dans le futur.',

        ];
    }
}