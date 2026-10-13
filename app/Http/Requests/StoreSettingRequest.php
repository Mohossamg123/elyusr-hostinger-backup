<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => [
                'required',
                'string',
                'max:255',
                'unique:settings,key',
            ],

            'value' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                'in:text,textarea,email,url,phone,image',
            ],

            'group' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }
}