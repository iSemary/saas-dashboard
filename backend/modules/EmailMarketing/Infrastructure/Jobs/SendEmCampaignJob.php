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

class SendEmCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly int $campaignId,
    ) {}

    public function handle(
        EmCampaignRepositoryInterface $campaignRepo,
        EmailSendingStrategyInterface $sendingStrategy,
    ): void {
        $campaign = $campaignRepo->findOrFail($this->campaignId);
        $sendingStrategy->send($campaign);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Email campaign send failed', [
            'campaign_id' => $this->campaignId,
            'error' => $exception->getMessage(),
        ]);
    }
}
