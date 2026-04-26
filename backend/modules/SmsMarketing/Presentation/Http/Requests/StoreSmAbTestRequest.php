<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmAbTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'campaign_id' => 'required|integer',
            'variant_name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'percentage' => 'nullable|integer|min:1|max:99',
        ];
    }
}
