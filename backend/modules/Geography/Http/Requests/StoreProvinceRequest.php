<?php

namespace Modules\Geography\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'country_id' => 'required|integer',
        ];
    }
}
