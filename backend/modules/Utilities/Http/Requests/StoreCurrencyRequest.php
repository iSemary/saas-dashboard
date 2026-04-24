<?php

namespace Modules\Utilities\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }
}
