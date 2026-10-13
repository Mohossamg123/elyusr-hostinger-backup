<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        $userId = is_object($user)
            ? $user->getKey()
            : $user;

        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'nullable',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'status' => 'required|in:active,inactive,suspended',

            'role_ids' => 'nullable|array',

            'role_ids.*' => [
                'integer',
                'exists:roles,id',
            ],
        ];
    }
}