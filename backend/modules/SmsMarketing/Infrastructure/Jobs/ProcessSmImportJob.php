<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\SmsMarketing\Domain\Strategies\Import\SmsImportStrategyInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmImportJobRepositoryInterface;

class ProcessSmImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly int $importJobId,
    ) {}

    public function handle(
        SmImportJobRepositoryInterface $importJobRepo,
        SmsImportStrategyInterface $importStrategy,
    ): void {
        $importJob = $importJobRepo->findOrFail($this->importJobId);
        $importStrategy->import($importJob);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('SMS import job failed', [
            'import_job_id' => $this->importJobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
