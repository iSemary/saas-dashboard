<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EmailMarketing\Domain\Strategies\Import\EmailImportStrategyInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmImportJobRepositoryInterface;

class ProcessEmImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly int $importJobId,
    ) {}

    public function handle(
        EmImportJobRepositoryInterface $importJobRepo,
        EmailImportStrategyInterface $importStrategy,
    ): void {
        $importJob = $importJobRepo->findOrFail($this->importJobId);
        $importStrategy->import($importJob);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Email import job failed', [
            'import_job_id' => $this->importJobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
