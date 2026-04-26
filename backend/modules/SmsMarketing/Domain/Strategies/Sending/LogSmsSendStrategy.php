<?php

namespace Modules\SmsMarketing\Domain\Strategies\Sending;

use Illuminate\Support\Str;
use Modules\SmsMarketing\Domain\Entities\SmCampaign;
use Modules\SmsMarketing\Domain\Entities\SmContact;
use Modules\SmsMarketing\Domain\ValueObjects\SmLogStatus;

class LogSmsSendStrategy implements SmsSendingStrategyInterface
{
    public function send(SmCampaign $campaign, SmContact $contact, array $variables = []): string
    {
        $messageId = 'sm-stub-' . Str::uuid()->toString();

        $campaign->sendingLogs()->create([
            'contact_id' => $contact->id,
            'status' => SmLogStatus::Sent->value,
            'sent_at' => now(),
            'provider_message_id' => $messageId,
            'cost' => 0.000000,
            'metadata' => ['strategy' => 'log', 'variables' => $variables],
        ]);

        return $messageId;
    }
}
