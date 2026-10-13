<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',

            'phone' => 'nullable|unique:users,phone',

            'password' => 'required|string|min:8',

            'status' => 'required|in:active,inactive,suspended',

            'role_ids' => 'required|array|min:1',

            'role_ids.*' => [
                'integer',
                'exists:roles,id',
            ],
        ];
    }
}