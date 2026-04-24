<?php

namespace Modules\Geography\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:countries,code',
            'phone_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }
}
