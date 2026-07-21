<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrainingModuleRequest extends FormRequest
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
        $module = $this->route('training_module');

        return [

            'training_id' => [
                'required',
                'exists:trainings,id',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('training_modules', 'code')
                    ->ignore($module->id),
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

            'duration_hours' => [
                'required',
                'integer',
                'min:1',
            ],

            'position' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('training_modules', 'position')
                    ->where(fn ($query) => $query->where(
                        'training_id',
                        $this->training_id
                    ))
                    ->ignore($module->id),
            ],

            'is_active' => [
                'sometimes',
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

            'training_id.required' => 'La formation est obligatoire.',
            'training_id.exists' => 'La formation sélectionnée est invalide.',

            'code.required' => 'Le code est obligatoire.',
            'code.unique' => 'Ce code existe déjà.',

            'title.required' => 'Le titre est obligatoire.',

            'duration_hours.required' => 'La durée est obligatoire.',
            'duration_hours.integer' => 'La durée doit être un nombre entier.',
            'duration_hours.min' => 'La durée doit être supérieure à zéro.',

            'position.required' => 'La position est obligatoire.',
            'position.integer' => 'La position doit être un entier.',
            'position.min' => 'La position doit être supérieure à zéro.',
            'position.unique' => 'Cette position est déjà utilisée pour cette formation.',

            'is_active.boolean' => 'Le statut doit être vrai ou faux.',

        ];
    }
}