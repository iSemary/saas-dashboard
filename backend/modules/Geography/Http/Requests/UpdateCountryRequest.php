<?php

namespace Modules\Geography\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
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
            'code' => "sometimes|required|string|max:5|unique:countries,code,{$id}",
            'phone_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }
}
