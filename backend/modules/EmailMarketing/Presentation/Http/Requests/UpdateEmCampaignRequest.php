<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:500',
            'template_id' => 'nullable|integer',
            'credential_id' => 'nullable|integer',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'body_html' => 'nullable|string',
            'body_text' => 'nullable|string',
            'status' => 'nullable|string|in:draft,scheduled,sending,sent,paused,cancelled',
            'scheduled_at' => 'nullable|date',
            'settings' => 'nullable|array',
            'contact_list_ids' => 'nullable|array',
            'contact_list_ids.*' => 'integer',
        ];
    }
}
