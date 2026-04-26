<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmWebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'url' => 'nullable|url|max:500',
            'events' => 'nullable|array',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
