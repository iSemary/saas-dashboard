<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:500',
            'body_html' => 'nullable|string',
            'body_text' => 'nullable|string',
            'thumbnail_url' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'variables' => 'nullable|array',
            'status' => 'nullable|string|in:draft,active,archived',
        ];
    }
}
