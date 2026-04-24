<?php

namespace Modules\Development\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:feature_flags,slug',
            'description' => 'nullable|string',
            'is_enabled' => 'nullable|boolean',
        ];
    }
}
