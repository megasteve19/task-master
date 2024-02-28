<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'filters.status' => ['nullable', 'string', Rule::in(['active', 'archived', 'trashed'])],
            'filters.search' => ['nullable', 'string'],
            'filters.assignees' => ['nullable', 'array'],
            'filters.assignees.*' => ['string', 'distinct', Rule::exists(User::class, 'id')],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $assignees = $this->input('filters.assignees');

        $this->merge([
            'filters' => [
                'status' => $this->input('filters.status', 'active'),
                'search' => $this->input('filters.search', ''),
                'assignees' => $this->filled('filters.assignees') && is_string($assignees)
                    ? explode(',', $assignees)
                    : $assignees,
            ],
        ]);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        // No need to throw anything here.
    }
}
