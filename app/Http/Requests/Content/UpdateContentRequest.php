<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Content;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_values(
                    array_filter(
                        array_map('trim', explode(',', $this->tags))
                    ),
                ),
            ]);
        }
    }

    public function rules(): array
    {
        $content = $this->route('content');

        $id = $content instanceof Content
            ? $content->id
            : $content;

        return [
            'type' => 'sometimes|required|in:article,program,faq,page',
            'status' => 'sometimes|required|in:draft,published',

            'slug' => 'sometimes|required|string|max:255|unique:contents,slug,' . $id,

            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'nullable|string|max:255',

            'summary_ar' => 'nullable|string',
            'summary_en' => 'nullable|string',

            'content_ar' => 'nullable|string',
            'content_en' => 'nullable|string',

            'cover_image' => 'nullable|image|max:5120',
            'svg' => 'nullable|string',

            'tags' => 'nullable',
            'author_id' => 'nullable|exists:users,id',
        ];
    }
}
