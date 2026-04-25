<?php

namespace App\Console\Commands\ProjectSetup;

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
        // Root database/migrations contains Passport (oauth_*) tables published by passport:install
        $this->call('migrate', [
            '--path' => 'database/migrations',
            '--database' => 'landlord',
        ]);

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

        $this->call('migrate', [
            '--path' => 'modules/*/database/migrations/landlord',
            '--database' => 'landlord',
        ]);

        $this->call('migrate', [
            '--path' => 'modules/*/database/migrations/shared',
            '--database' => 'landlord',
        ]);

        $this->info('Landlord migrations executed successfully.');
        return 0;
    }
}
