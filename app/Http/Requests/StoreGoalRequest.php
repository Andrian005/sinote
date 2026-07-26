<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'goal_type' => ['required', 'string', Rule::in(['time_bound', 'ongoing'])],
            'target_date' => [
                Rule::requiredIf(fn () => $this->input('goal_type') === 'time_bound'),
                'nullable',
                'date',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['title' => trim($this->input('title', ''))]);
    }
}
