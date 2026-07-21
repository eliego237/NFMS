<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    public function messages(): array
{
    return [

        'code.required' => 'Le code est obligatoire.',
        'code.unique' => 'Le code est déjà utilisé.',

        'title.required' => 'Le titre est obligatoire.',

        'category.required' => 'La catégorie est obligatoire.',

        'price.required' => 'Le prix est obligatoire.',
        'price.numeric' => 'Le prix doit être un nombre.',

        'duration_months.required' => 'La durée est obligatoire.',
        'duration_months.integer' => 'La durée doit être un entier.',

        'certificate.required' => 'Le certificat est obligatoire.',

    ];
}

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $trainingId = $this->route('training')->id;

        return [

            'code' => [
                'required',
                'string',
                'max:20',
                'unique:trainings,code,' . $trainingId,
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'duration_months' => [
                'required',
                'integer',
                'min:1',
            ],

            'certificate' => [
                'required',
                'string',
                'max:100',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ];
    }
}