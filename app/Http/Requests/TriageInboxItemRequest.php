<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TriageInboxItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization against InboxItemPolicy::triage is handled in the Action.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * project_id ownership by the authenticated user is validated in
     * TriageInboxItem Action, not here — Form Request only validates
     * existence in the projects table.
     */
    public function rules(): array
    {
        return [
            'target_type' => ['required', 'string', Rule::in(['task', 'note', 'project'])],
            'project_id' => [
                Rule::requiredIf(fn () => $this->input('target_type') === 'project'),
                'nullable',
                'string',
                Rule::exists('projects', 'id'),
            ],
        ];
    }
}
