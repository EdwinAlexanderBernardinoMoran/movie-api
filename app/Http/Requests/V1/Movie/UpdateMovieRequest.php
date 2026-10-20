<?php

namespace App\Http\Requests\V1\Movie;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovieRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255', Rule::unique('movies', 'title')->ignore($this->route('movie'))],
            'year' => ['sometimes', 'integer', 'between:1888,2100'],
            'synopsis' => ['sometimes', 'string'],
            'rating' => ['sometimes', 'numeric', 'between:0,10'],
            'director' => ['sometimes', 'string', 'max:255'],
            'duration' => ['sometimes', 'integer', 'min:1'],
            'poster' => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'genre_ids' => ['sometimes', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
        ];
    }
}
