<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('permission')->id;

        return [
            'name' => "required|unique:permissions,name,$id",
            'slug' => "required|unique:permissions,slug,$id",
            'module' => 'required|string',
            'description' => 'nullable|string',
        ];
    }
}
