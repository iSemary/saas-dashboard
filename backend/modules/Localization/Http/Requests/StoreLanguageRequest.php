<?php

namespace Modules\Localization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code',
            'direction' => 'nullable|string|in:ltr,rtl',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];
    }
}
