<?php

namespace Modules\HR\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\HR\Domain\Entities\Employee;

class BirthdayReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = now()->format('m-d');
        $count = Employee::query()
            ->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = ?", [$today])
            ->count();

        Log::info('HR birthday reminder job executed', ['birthdays_today' => $count]);
    }
}
