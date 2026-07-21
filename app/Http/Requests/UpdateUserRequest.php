<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Autoriser la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Préparer les données avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim(
                ($this->first_name ?? '') . ' ' . ($this->last_name ?? '')
            ),
            'role' => $this->roles[0] ?? null,
        ]);
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

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')->id),
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
                'nullable',
                'confirmed',
                'min:8',
            ],

            'role' => [
                'required',
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

            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'password.confirmed' => 'La confirmation du mot de passe est incorrecte.',

            'role.exists' => 'Le rôle sélectionné est invalide.',

        ];
    }
}