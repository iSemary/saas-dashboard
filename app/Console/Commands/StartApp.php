<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class StartApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running all required commands for first start';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commands = [
            ['command' => 'storage:link', 'description' => "Creating storage symbolic link"],
            ['command' => 'landlord:migrate', 'description' => "Running landlord migrations"],
            ['command' => 'db:seed', 'parameters' => ['--class' => 'Database\\Seeders\\Landlord\\LandlordTenantSeeder'], 'description' => "Seeding landlord tenants"],
            ['command' => 'db:seed', 'parameters' => ['--class' => 'Database\\Seeders\\Landlord\\RolePermissionSeeder'], 'description' => "Seeding roles and permissions"],
            ['command' => 'db:seed', 'parameters' => ['--class' => 'Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder'], 'description' => "Seeding landlord users"],
            ['command' => 'db:seed', 'parameters' => ['--class' => 'Modules\\Utilities\\Database\\Seeders\\ModulesSeeder'], 'description' => "Seeding modules"],
            ['command' => 'db:seed', 'parameters' => ['--class' => 'Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder'], 'description' => "Seeding configurations"],
        ];

        foreach ($commands as $command) {
            $this->info("\nRunning: {$command['description']}");

            try {
                if (isset($command['parameters'])) {
                    Artisan::call($command['command'], $command['parameters']);
                } else {
                    Artisan::call($command['command']);
                }

                $output = Artisan::output();

                $this->info("Output: " . trim($output));
                Log::info("Successfully executed '{$command['command']}': {$command['description']}");

                $this->line("\n" . str_repeat('-', 50));
            } catch (\Exception $e) {
                $this->error("Failed to execute '{$command['command']}': " . $e->getMessage());
                Log::error("Failed to execute '{$command['command']}': " . $e->getMessage());

                if ($this->confirm('Do you want to continue with the remaining commands?', true)) {
                    continue;
                }

                return 1;
            }
        }

        $this->info("\nAll startup commands completed successfully!");
        return 0;
    }
}
