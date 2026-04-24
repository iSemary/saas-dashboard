<?php

namespace Modules\Localization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|string',
            'group' => 'nullable|string|max:255',
            'language_id' => 'sometimes|required|integer',
        ];
    }
}
