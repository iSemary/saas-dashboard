<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\SmsMarketing\Domain\Strategies\Sending\SmsSendingStrategyInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmCampaignRepositoryInterface;

class SendSmCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly int $campaignId,
    ) {}

    public function handle(
        SmCampaignRepositoryInterface $campaignRepo,
        SmsSendingStrategyInterface $sendingStrategy,
    ): void {
        $campaign = $campaignRepo->findOrFail($this->campaignId);
        $sendingStrategy->send($campaign);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('SMS campaign send failed', [
            'campaign_id' => $this->campaignId,
            'error' => $exception->getMessage(),
        ]);
    }
}
