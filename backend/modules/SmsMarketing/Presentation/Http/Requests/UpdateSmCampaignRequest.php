<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'template_id' => 'nullable|integer',
            'credential_id' => 'nullable|integer',
            'body' => 'nullable|string',
            'status' => 'nullable|string|in:draft,scheduled,sending,sent,paused,cancelled',
            'scheduled_at' => 'nullable|date',
            'settings' => 'nullable|array',
            'contact_list_ids' => 'nullable|array',
            'contact_list_ids.*' => 'integer',
        ];
    }
}
