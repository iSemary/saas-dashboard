<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'from_address' => 'sometimes|required|email',
            'from_name' => 'sometimes|required|string|max:255',
            'mailer' => 'sometimes|required|in:smtp,ses,mailgun,postmark',
            'host' => 'sometimes|required|string|max:255',
            'port' => 'sometimes|required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'encryption' => 'nullable|in:tls,ssl',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
