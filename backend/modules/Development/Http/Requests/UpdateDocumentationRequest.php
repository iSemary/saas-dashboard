<?php

namespace Modules\Development\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
        ];
    }
}
