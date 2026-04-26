<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmCredentialRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'provider' => 'nullable|string|in:twilio,vonage,messagebird,mock',
            'account_sid' => 'nullable|string|max:255',
            'auth_token' => 'nullable|string|max:255',
            'from_number' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
