<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmAbTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'campaign_id' => 'required|integer',
            'variant_name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:500',
            'body_html' => 'nullable|string',
            'percentage' => 'nullable|integer|min:1|max:99',
        ];
    }
}
