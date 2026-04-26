<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmAutomationRuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string|in:contact_added,sms_sent,sms_delivered,sms_failed,opted_out',
            'conditions' => 'nullable|array',
            'action_type' => 'nullable|string|in:send_campaign,add_to_list,remove_from_list,webhook',
            'action_config' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ];
    }
}
