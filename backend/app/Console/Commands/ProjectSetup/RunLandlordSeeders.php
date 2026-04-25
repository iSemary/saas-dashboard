<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class RunLandlordSeeders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'landlord:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run landlord seeders for both paths';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $this->info('🌱 Running Landlord Seeders...');
        $this->newLine();

        $seederPaths = [
            'database/seeders/Landlord',
            'database/seeders/landlord',
            'modules/*/Database/Seeders/Landlord',
            'modules/*/Database/Seeders/landlord',
            'modules/*/Database/Seeders/shared',
            'modules/*/Database/seeders/Landlord',
            'modules/*/Database/seeders/landlord',
            'modules/*/Database/seeders/shared',
            'modules/*/database/Seeders/Landlord',
            'modules/*/database/Seeders/landlord',
            'modules/*/database/Seeders/shared',
            'modules/*/database/seeders/Landlord',
            'modules/*/database/seeders/landlord',
            'modules/*/database/seeders/shared',
        ];

        $totalSeeders = 0;
        foreach ($seederPaths as $path) {
            $count = $this->runSeedersFromPath($path);
            $totalSeeders += $count;
        }

        $this->newLine();
        $this->info("✅ Landlord seeders executed successfully. Total seeders run: {$totalSeeders}");
        return 0;
    }

    /**
     * Run all seeders from a specific path
     */
    private function runSeedersFromPath($path)
    {
        $expandedPaths = glob(base_path($path));
        $count = 0;

        foreach ($expandedPaths as $expandedPath) {
            if (!is_dir($expandedPath)) {
                continue;
            }

            $seederFiles = File::glob($expandedPath . '/*Seeder.php');

            if (empty($seederFiles)) {
                continue;
            }

            $this->line("📂 Found path: {$expandedPath}");

            foreach ($seederFiles as $seederFile) {
                $className = $this->getSeederClassName($seederFile);

                if ($className) {
                    try {
                        $this->line("   Seeding: {$className}");
                        Artisan::call('db:seed', [
                            '--class' => $className,
                            '--database' => 'landlord',
                            '--force' => true
                        ]);
                        $this->line("   ✅ {$className} seeded successfully");
                        $count++;
                    } catch (\Exception $e) {
                        $this->warn("   ⚠️  Warning: {$className} - {$e->getMessage()}");
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Get seeder class name from file path
     */
    private function getSeederClassName($filePath)
    {
        $relativePath = str_replace(base_path() . '/', '', $filePath);
        $relativePath = str_replace('.php', '', $relativePath);

        // Handle database/seeders/Landlord (uppercase)
        if (str_starts_with($relativePath, 'database/seeders/Landlord/')) {
            $className = str_replace('database/seeders/Landlord/', '', $relativePath);
            return 'Database\\Seeders\\Landlord\\' . str_replace('/', '\\', $className);
        }

        // Handle database/seeders/landlord (lowercase)
        if (str_starts_with($relativePath, 'database/seeders/landlord/')) {
            $className = str_replace('database/seeders/landlord/', '', $relativePath);
            return 'Database\\Seeders\\landlord\\' . str_replace('/', '\\', $className);
        }

        // Handle modules/*/Database/Seeders/Landlord or landlord (case-insensitive)
        if (preg_match('#modules/([^/]+)/Database/Seeders/([Ll]andlord|[Ss]hared)/(.+)#', $relativePath, $matches)) {
            $moduleName = $matches[1];
            $folder = ucfirst($matches[2]); // Capitalize first letter
            $seederName = $matches[3];
            return "Modules\\{$moduleName}\\Database\\Seeders\\{$folder}\\" . str_replace('/', '\\', $seederName);
        }

        // Handle modules/*/Database/seeders/Landlord or landlord (case-insensitive)
        if (preg_match('#modules/([^/]+)/Database/seeders/([Ll]andlord|[Ss]hared)/(.+)#', $relativePath, $matches)) {
            $moduleName = $matches[1];
            $folder = ucfirst($matches[2]); // Capitalize first letter
            $seederName = $matches[3];
            return "Modules\\{$moduleName}\\Database\\Seeders\\{$folder}\\" . str_replace('/', '\\', $seederName);
        }

        // Handle modules/*/database/Seeders/Landlord or landlord (case-insensitive)
        if (preg_match('#modules/([^/]+)/database/Seeders/([Ll]andlord|[Ss]hared)/(.+)#', $relativePath, $matches)) {
            $moduleName = $matches[1];
            $folder = ucfirst($matches[2]); // Capitalize first letter
            $seederName = $matches[3];
            return "Modules\\{$moduleName}\\Database\\Seeders\\{$folder}\\" . str_replace('/', '\\', $seederName);
        }

        // Handle modules/*/database/seeders/Landlord or landlord (case-insensitive)
        if (preg_match('#modules/([^/]+)/database/seeders/([Ll]andlord|[Ss]hared)/(.+)#', $relativePath, $matches)) {
            $moduleName = $matches[1];
            $folder = ucfirst($matches[2]); // Capitalize first letter
            $seederName = $matches[3];
            return "Modules\\{$moduleName}\\Database\\Seeders\\{$folder}\\" . str_replace('/', '\\', $seederName);
        }

        return null;
    }
}
