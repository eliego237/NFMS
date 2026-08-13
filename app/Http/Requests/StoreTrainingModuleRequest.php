<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingModuleRequest extends FormRequest
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

            /*
            |--------------------------------------------------------------------------
            | Formation
            |--------------------------------------------------------------------------
            */

            'training_id' => [
                'required',
                'integer',
                'exists:trainings,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Code du module
            |--------------------------------------------------------------------------
            */

            'code' => [
                'required',
                'string',
                'max:50',

                Rule::unique('training_modules', 'code')
                    ->whereNull('deleted_at'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Titre
            |--------------------------------------------------------------------------
            */

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'description' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Durée
            |--------------------------------------------------------------------------
            */

            'duration_hours' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Position
            |--------------------------------------------------------------------------
            |
            | Une même formation peut avoir plusieurs modules.
            |
            | Exemple :
            |
            | Coiffure mixte
            | position 1 → Coiffure Femme
            | position 2 → Coiffure Homme
            | position 3 → Tresses
            |
            */

            'position' => [
                'required',
                'integer',
                'min:1',

                Rule::unique('training_modules', 'position')
                    ->where(function ($query) {
                        return $query
                            ->where(
                                'training_id',
                                $this->training_id
                            )
                            ->whereNull('deleted_at');
                    }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

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

            'training_id.required' =>
                'La formation est obligatoire.',

            'training_id.integer' =>
                'La formation est invalide.',

            'training_id.exists' =>
                'La formation sélectionnée est invalide.',


            'code.required' =>
                'Le code du module est obligatoire.',

            'code.unique' =>
                'Ce code de module existe déjà.',


            'title.required' =>
                'Le titre du module est obligatoire.',

            'title.string' =>
                'Le titre du module doit être une chaîne de caractères.',


            'duration_hours.required' =>
                'La durée du module est obligatoire.',

            'duration_hours.integer' =>
                'La durée doit être un nombre entier.',

            'duration_hours.min' =>
                'La durée doit être supérieure à zéro.',


            'position.required' =>
                'La position du module est obligatoire.',

            'position.integer' =>
                'La position doit être un entier.',

            'position.min' =>
                'La position doit être supérieure à zéro.',

            'position.unique' =>
                'Cette position est déjà utilisée pour cette formation.',


            'is_active.boolean' =>
                'Le statut doit être vrai ou faux.',

        ];
    }
}