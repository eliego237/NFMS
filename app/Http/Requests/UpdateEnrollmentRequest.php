<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
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
        return [

            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'training_id' => [
                'required',
                'exists:trainings,id',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'academic_year' => [
                'nullable',
                'string',
                'max:20',
            ],

            'enrolled_at' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

        ];
    }
}