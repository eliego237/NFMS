<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'password'   => ['required', 'confirmed', 'min:8'],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.email'         => 'L\'adresse email est invalide.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.confirmed'  => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }

    /**
     * Format des erreurs de validation.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Les données envoyées sont invalides.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}