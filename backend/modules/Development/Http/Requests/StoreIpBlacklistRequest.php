<?php

namespace Modules\Development\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpBlacklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ip_address' => 'required|string|max:45',
        ];
    }
}
