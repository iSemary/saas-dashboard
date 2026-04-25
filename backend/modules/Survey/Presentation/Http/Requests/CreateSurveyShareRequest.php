<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyShareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => 'required|string|in:link,email,sms,qr_code,embed,social',
            'config' => 'nullable|array',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}
