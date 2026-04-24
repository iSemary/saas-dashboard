<?php

namespace Modules\Localization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'group' => 'nullable|string|max:255',
            'language_id' => 'required|integer',
        ];
    }
}
