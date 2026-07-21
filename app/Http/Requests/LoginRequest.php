<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
{
    return [

        'email.required' => 'L\'adresse email est obligatoire.',

        'email.email' => 'Le format de l\'adresse email est invalide.',

        'password.required' => 'Le mot de passe est obligatoire.',

    ];
}

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}