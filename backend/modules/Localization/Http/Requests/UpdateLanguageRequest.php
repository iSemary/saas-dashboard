<?php

namespace Modules\Localization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => "sometimes|required|string|max:10|unique:languages,code,{$id}",
            'direction' => 'nullable|string|in:ltr,rtl',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];
    }
}
