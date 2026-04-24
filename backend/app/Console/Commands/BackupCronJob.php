<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use iSemary\BackupSentry\BackupSentry;
use Modules\Development\Entities\Backup;

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
        try {
            $this->performBackup();
            $enabledServices = $this->getEnabledServices();

            $this->createBackupRecord($enabledServices);

            $this->info('Backup cronjob executed successfully.');
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Perform the backup operation
     */
    private function performBackup(): void
    {
        (new BackupSentry)->run();
    }

    /**
     * Get all enabled backup services and settings
     */
    private function getEnabledServices(): array
    {
        $config = config('BackupSentry.backup');
        $enabledServices = [];

        $this->processCloudServices($config, $enabledServices);
        $this->processMailChannel($config, $enabledServices);
        $this->processCustomChannels($config, $enabledServices);
        $this->processBackupTypes($config, $enabledServices);

        return $enabledServices;
    }

    /**
     * Process cloud services configuration
     */
    private function processCloudServices(array $config, array &$enabledServices): void
    {
        if (!empty($config['cloud_services'])) {
            foreach ($config['cloud_services'] as $service => $settings) {
                if ($settings['allow']) {
                    $enabledServices[$service] = true;
                }
            }
        }
    }

    /**
     * Process mail channel configuration
     */
    private function processMailChannel(array $config, array &$enabledServices): void
    {
        if ($config['mail']['allow']) {
            $enabledServices['mail'] = true;
        }
    }

    /**
     * Process custom backup channels
     */
    private function processCustomChannels(array $config, array &$enabledServices): void
    {
        foreach ($config['channels'] as $channel => $settings) {
            if ($settings['allow']) {
                $enabledServices[$channel] = true;
            }
        }
    }

    /**
     * Process backup type settings
     */
    private function processBackupTypes(array $config, array &$enabledServices): void
    {
        if ($config['database']['allow']) {
            $enabledServices['database'] = true;
        }
        if ($config['full_project']) {
            $enabledServices['full_project'] = true;
        }
        if ($config['storage_only']) {
            $enabledServices['storage_only'] = true;
        }
    }

    /**
     * Create a backup record in the database
     */
    private function createBackupRecord(array $enabledServices): void
    {
        Backup::create([
            'name' => now(),
            'metadata' => json_encode($enabledServices),
        ]);
    }
}
