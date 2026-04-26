<?php

namespace Modules\EmailMarketing\Infrastructure\Strategies;

use Illuminate\Support\Str;
use Modules\EmailMarketing\Domain\Entities\EmCampaign;
use Modules\EmailMarketing\Domain\Entities\EmContact;
use Modules\EmailMarketing\Domain\Strategies\EmSendingStrategyInterface;
use Modules\EmailMarketing\Domain\ValueObjects\EmLogStatus;

class LogEmailSendStrategy implements EmSendingStrategyInterface
{
    public function send(EmCampaign $campaign, EmContact $contact, array $variables = []): string
    {
        $messageId = 'em-stub-' . Str::uuid()->toString();

        $campaign->sendingLogs()->create([
            'contact_id' => $contact->id,
            'status' => EmLogStatus::Sent->value,
            'sent_at' => now(),
            'message_id' => $messageId,
            'metadata' => ['strategy' => 'log', 'variables' => $variables],
        ]);

        return $messageId;
    }
}
