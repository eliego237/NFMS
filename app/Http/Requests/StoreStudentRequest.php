<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:M,F',
            'birth_date' => 'nullable|date',

            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|unique:students,email',
            'address' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',

            'formation' => 'required|string|max:150',
            'registration_date' => 'required|date',

            'photo' => 'nullable|string',
            'status' => 'boolean',
        ];
    }
}