<?php

namespace App\Http\Requests\V1\Movie;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePosterRequest extends FormRequest
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
            'poster' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }

    /**
     * Get the custom error messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'poster.required' => 'The poster is required.',
            'poster.image' => 'The poster must be an image.',
            'poster.mimes' => 'The poster must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'poster.max' => 'The poster may not be greater than 2048 kilobytes.',
        ];
    }
}
