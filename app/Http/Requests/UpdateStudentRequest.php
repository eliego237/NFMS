<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student')->id;

        return [

            /*
            |--------------------------------------------------------------------------
            | Informations personnelles
            |--------------------------------------------------------------------------
            */

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'gender' => [
                'required',
                Rule::in(['M', 'F']),
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            /*
            |--------------------------------------------------------------------------
            | Coordonnées
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId),
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            'photo' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            'status' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'first_name.required' => 'Le prénom est obligatoire.',

            'last_name.required' => 'Le nom est obligatoire.',

            'gender.required' => 'Le sexe est obligatoire.',

            'gender.in' => 'Le sexe doit être M ou F.',

            'birth_date.date' => 'La date de naissance est invalide.',

            'birth_date.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',

            'phone.required' => 'Le téléphone est obligatoire.',

            'email.email' => 'Adresse email invalide.',

            'email.unique' => 'Cette adresse email existe déjà.',

        ];
    }
}