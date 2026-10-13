<?php

namespace App\Http\Requests\Donor;

use Illuminate\Contracts\Validation\ValidationRule;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'name'=>'required',
        'email'=>'nullable|email',
        'phone'=>'nullable',
        'address'=>'nullable'
    ];
}
}
