<?php

namespace Modules\Utilities\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
            'code' => "sometimes|required|string|max:10|unique:currencies,code,{$id}",
            'symbol' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }
}
