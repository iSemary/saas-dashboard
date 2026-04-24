<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string|max:255',
            'mailer' => 'required|in:smtp,ses,mailgun,postmark',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'encryption' => 'nullable|in:tls,ssl',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
