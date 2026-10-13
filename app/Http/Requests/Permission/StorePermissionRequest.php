<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:permissions,name',
            'slug' => 'required|unique:permissions,slug',
            'module' => 'required|string',
            'description' => 'nullable|string',
        ];
    }
}
