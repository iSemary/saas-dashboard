<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
