<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Verify2FARequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temp_token' => 'required|string',
            'code' => 'required|string|size:6',
            'subdomain' => 'nullable|string|max:64',
        ];
    }
}
