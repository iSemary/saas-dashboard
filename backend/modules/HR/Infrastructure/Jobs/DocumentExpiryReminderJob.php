<?php

namespace Modules\HR\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\HR\Domain\Entities\EmployeeDocument;

class DocumentExpiryReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $in30Days = now()->addDays(30)->toDateString();
        $count = EmployeeDocument::query()
            ->whereDate('expiry_date', '<=', $in30Days)
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->count();

        Log::info('HR document expiry reminder job executed', ['expiring_documents' => $count]);
    }
}
