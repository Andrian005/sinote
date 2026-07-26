<?php

namespace App\Http\Requests\Tagging;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates input for creating or assigning a Tag.
 *
 * Rules derived from FSD 4.1 Validation Rules:
 *   - name: 1–50 characters, no commas (commas break URL/filter parsing).
 *
 * Note: uniqueness per user is enforced at the database level (unique constraint)
 * and handled gracefully by CreateTag Action (returns existing tag rather than
 * throwing a validation error — FSD 4.1 Exception Handling).
 */
class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authentication is enforced at the route level (auth middleware).
        // No additional authorization check needed here — the Action decides
        // whether the authenticated user may own the resulting tag.
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            // Required, 1–50 chars, no commas — FSD 4.1
            'name' => ['required', 'string', 'max:50', 'not_regex:/,/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.max' => 'Tag name may not exceed 50 characters.',
            'name.not_regex' => 'Tag name may not contain commas.',
        ];
    }
}
