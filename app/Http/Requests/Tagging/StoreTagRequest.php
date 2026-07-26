<?php

namespace App\Http\Requests\Tagging;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Name: 1–50 characters, no commas.
 * Uniqueness per user is handled by CreateTag Action (firstOrCreate — no error thrown).
 */
class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'not_regex:/,/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.max' => 'Tag name may not exceed 50 characters.',
            'name.not_regex' => 'Tag name may not contain commas.',
        ];
    }
}
