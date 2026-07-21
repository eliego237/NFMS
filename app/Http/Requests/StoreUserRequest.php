<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Autoriser la requête.
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

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'photo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'exists:roles,name',
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

            'email.required' => 'L\'adresse email est obligatoire.',

            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'password.required' => 'Le mot de passe est obligatoire.',

            'password.confirmed' => 'La confirmation du mot de passe est incorrecte.',

            'roles.required' => 'Veuillez sélectionner au moins un rôle.',

            'roles.*.exists' => 'Le rôle sélectionné est invalide.',

        ];
    }

    /**
     * Format uniforme des erreurs.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Les données envoyées sont invalides.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}