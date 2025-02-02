<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunLandlordMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'landlord:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run landlord migrations for both paths';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $this->call('migrate', [
            '--path' => 'database/migrations/shared',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'database/migrations/landlord',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'modules/*/Database/Migrations/landlord',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'modules/*/Database/Migrations/shared',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'modules/*/Database/migrations/landlord',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'modules/*/Database/migrations/shared',
            '--database' => 'landlord',
        ]);

        $this->info('Landlord migrations executed successfully.');
        return 0;
    }
}
