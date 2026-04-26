<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmCredentialRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'provider' => 'required|string|in:smtp,ses,mailgun,sendgrid',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer',
            'username' => 'nullable|string|max:255',
            'password' => 'required|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
