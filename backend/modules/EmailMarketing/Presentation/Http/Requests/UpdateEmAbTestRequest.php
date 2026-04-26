<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmAbTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'variant_name' => 'sometimes|string|max:255',
            'subject' => 'nullable|string|max:500',
            'body_html' => 'nullable|string',
            'percentage' => 'nullable|integer|min:1|max:99',
            'winner' => 'nullable|string|max:255',
        ];
    }
}
