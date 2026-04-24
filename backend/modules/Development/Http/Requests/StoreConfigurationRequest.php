<?php

namespace Modules\Development\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'type' => 'nullable|string|max:50',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ];
    }
}
