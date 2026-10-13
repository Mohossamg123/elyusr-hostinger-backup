<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'target_amount'=>'required|numeric|min:0',
            'collected_amount'=>'nullable|numeric|min:0',
            'is_active'=>'boolean',
        ];
    }
}
