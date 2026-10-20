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

            /*
            |--------------------------------------------------------------------------
            | Paiement classique
            |--------------------------------------------------------------------------
            */

            'enrollment_id' => [
                'nullable',
                'integer',
                'exists:enrollments,id',
                'required_without:allocations',
            ],

            /*
            |--------------------------------------------------------------------------
            | Montant total du paiement
            |--------------------------------------------------------------------------
            */

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Paiement groupé
            |--------------------------------------------------------------------------
            */

            'allocations' => [
                'nullable',
                'array',
                'min:1',
                'required_without:enrollment_id',
            ],

            'allocations.*.enrollment_id' => [
                'required_with:allocations',
                'integer',
                'exists:enrollments,id',
                'distinct',
            ],

            'allocations.*.amount' => [
                'required_with:allocations',
                'numeric',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Moyen de paiement
            |--------------------------------------------------------------------------
            */

            'payment_method_id' => [
                'required',
                'exists:payment_methods,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            /*
            |--------------------------------------------------------------------------
            | Informations complémentaires
            |--------------------------------------------------------------------------
            */

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

            'enrollment_id.required_without' =>
                'Veuillez sélectionner une inscription ou plusieurs inscriptions.',

            'enrollment_id.exists' =>
                'Cette inscription est introuvable.',

            'amount.required' =>
                'Le montant est obligatoire.',

            'amount.numeric' =>
                'Le montant doit être numérique.',

            'amount.min' =>
                'Le montant doit être supérieur à zéro.',

            'allocations.required_without' =>
                'Veuillez sélectionner au moins une inscription.',

            'allocations.array' =>
                'Les allocations de paiement sont invalides.',

            'allocations.min' =>
                'Veuillez sélectionner au moins une inscription.',

            'allocations.*.enrollment_id.required_with' =>
                'L’inscription de chaque allocation est obligatoire.',

            'allocations.*.enrollment_id.exists' =>
                'Une des inscriptions sélectionnées est introuvable.',

            'allocations.*.enrollment_id.distinct' =>
                'Une inscription ne peut apparaître qu’une seule fois.',

            'allocations.*.amount.required_with' =>
                'Le montant de chaque allocation est obligatoire.',

            'allocations.*.amount.numeric' =>
                'Le montant d’une allocation doit être numérique.',

            'allocations.*.amount.min' =>
                'Le montant d’une allocation doit être supérieur à zéro.',

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