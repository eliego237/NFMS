<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'student_id' => 'required|exists:students,id',

            'training_id' => 'required|exists:trainings,id',

            'registration_number' => 'required|string|max:100|unique:enrollments',

            'registration_date' => 'required|date',

            'status' => 'nullable|string',

            'notes' => 'nullable|string',

        ];
    }
}