<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EmailMarketing\Domain\Strategies\Sending\EmailSendingStrategyInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmCampaignRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmContactRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmSendingLogRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\SendingLog\CreateEmSendingLogDTO;

class SendEmBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public readonly int $campaignId,
        public readonly array $contactIds,
    ) {}

    public function handle(
        EmCampaignRepositoryInterface $campaignRepo,
        EmContactRepositoryInterface $contactRepo,
        EmSendingLogRepositoryInterface $logRepo,
        EmailSendingStrategyInterface $sendingStrategy,
    ): void {
        $campaign = $campaignRepo->findOrFail($this->campaignId);

        foreach ($this->contactIds as $contactId) {
            $contact = $contactRepo->findOrFail($contactId);

            $log = $logRepo->create(
                (new CreateEmSendingLogDTO(
                    campaign_id: $this->campaignId,
                    contact_id: $contactId,
                    status: 'queued',
                ))->toArray()
            );

            try {
                $sendingStrategy->sendToContact($campaign, $contact);
                $log->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                $log->update([
                    'status' => 'failed',
                    'failed_reason' => $e->getMessage(),
                    'failed_at' => now(),
                ]);
            }
        }
    }
}
