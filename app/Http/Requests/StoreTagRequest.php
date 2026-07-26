<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:50', 'regex:/^[^,]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.min' => 'Tag name must be at least 1 character.',
            'name.max' => 'Tag name must not exceed 50 characters.',
            'name.regex' => 'Tag name cannot contain commas.',
        ];
    }
}
