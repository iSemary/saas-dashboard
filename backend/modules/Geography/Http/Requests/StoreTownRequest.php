<?php

namespace Modules\Geography\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'city_id' => 'required|integer',
        ];
    }
}
