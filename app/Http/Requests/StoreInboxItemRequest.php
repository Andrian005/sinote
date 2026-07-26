<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInboxItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization against the Policy is handled in the Action/Controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Content min:1 is enforced on the trimmed value to prevent
     * submissions that contain only whitespace (FSD 1.1).
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:5000'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Strip leading/trailing whitespace before rules are applied.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'content' => trim($this->input('content', '')),
        ]);
    }
}
