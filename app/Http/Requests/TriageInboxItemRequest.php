<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TriageInboxItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_type' => ['required', 'string', Rule::in(['task', 'note', 'project'])],
            // project_id ownership is validated in TriageInboxItem Action, not here
            'project_id' => [
                Rule::requiredIf(fn () => $this->input('target_type') === 'project'),
                'nullable',
                'string',
                Rule::exists('projects', 'id'),
            ],
        ];
    }
}
