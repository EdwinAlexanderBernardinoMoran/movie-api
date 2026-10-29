<?php

namespace App\Http\Requests\V1\AI;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecommendationsRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'genres' => ['required', 'array', 'min:1', 'max:5'],
            'genres.*' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get the custom error messages for the validation rules.
     */
    public function messages(): array
    {
        return [
            'genres.required' => 'El campo géneros es obligatorio.',
            'genres.array' => 'El campo géneros debe ser un arreglo.',
            'genres.min' => 'Debes seleccionar al menos un género.',
            'genres.max' => 'No puedes seleccionar más de cinco géneros.',
            'genres.*.required' => 'Cada género es obligatorio.',
            'genres.*.string' => 'Cada género debe ser una cadena de texto.',
            'genres.*.max' => 'Cada género no puede tener más de 100 caracteres.',
        ];
    }
}
