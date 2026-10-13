<?php

namespace App\Http\Requests\VolunteerCampaign;

use Illuminate\Foundation\Http\FormRequest;

class InviteVolunteersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'volunteer_ids' => ['required', 'array', 'min:1'],
            'volunteer_ids.*' => ['integer', 'exists:volunteers,id'],
        ];
    }
}
