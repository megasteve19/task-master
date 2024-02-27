<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'query' => 'string',
            'except' => ['array', 'nullable'],
            'except.*' => ['string', 'distinct', Rule::exists(User::class, 'id')],
            'limit' => 'integer|min:1|max:30',
        ];
    }
}
