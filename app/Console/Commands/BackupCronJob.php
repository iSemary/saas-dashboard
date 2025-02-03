<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the application and save it in the cloud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Example cronjob executed successfully.');
    }
}
