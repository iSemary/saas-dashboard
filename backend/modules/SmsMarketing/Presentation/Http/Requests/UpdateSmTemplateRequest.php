<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'body' => 'nullable|string',
            'variables' => 'nullable|array',
            'status' => 'nullable|string|in:draft,active,archived',
        ];
    }
}
