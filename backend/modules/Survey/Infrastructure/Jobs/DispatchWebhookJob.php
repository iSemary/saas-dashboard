<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Survey\Domain\Entities\SurveyWebhook;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [60, 300, 900]; // 1min, 5min, 15min

    public function __construct(
        public SurveyWebhook $webhook,
        public array $payload
    ) {}

    public function handle(): void
    {
        $payload = $this->webhook->generatePayload($this->payload);
        $signature = $this->webhook->signPayload($payload);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Survey-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->webhook->url, $payload);

            if ($response->successful()) {
                $this->webhook->recordTrigger();
                Log::info('Webhook dispatched successfully', [
                    'webhook_id' => $this->webhook->id,
                    'url' => $this->webhook->url,
                ]);
            } else {
                Log::warning('Webhook returned non-success status', [
                    'webhook_id' => $this->webhook->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $this->release($this->backoff[$this->attempts() - 1] ?? 900);
            }
        } catch (\Exception $e) {
            Log::error('Webhook dispatch failed', [
                'webhook_id' => $this->webhook->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
