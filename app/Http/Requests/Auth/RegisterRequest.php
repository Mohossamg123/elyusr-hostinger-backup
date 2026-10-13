<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'role' => [
                'required',
                Rule::in([
                    'donor',
                    'volunteer'
                ]),
            ],

        ];
    }
}
