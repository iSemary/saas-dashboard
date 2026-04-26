<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmContactRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:50',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'custom_fields' => 'nullable|array',
            'status' => 'nullable|string|in:active,opted_out,invalid',
        ];
    }
}
