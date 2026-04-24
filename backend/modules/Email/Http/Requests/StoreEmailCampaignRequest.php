<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'template_id' => 'nullable|integer',
            'status' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ];
    }
}
