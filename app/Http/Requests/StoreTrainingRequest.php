<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
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

            'code' => [
                'required',
                'string',
                'max:20',
                'unique:trainings,code',
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