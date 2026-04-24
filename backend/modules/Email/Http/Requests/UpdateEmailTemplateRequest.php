<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'variables' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
