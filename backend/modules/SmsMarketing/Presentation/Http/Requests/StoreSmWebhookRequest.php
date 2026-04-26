<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmWebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'events.*' => 'string',
            'secret' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
