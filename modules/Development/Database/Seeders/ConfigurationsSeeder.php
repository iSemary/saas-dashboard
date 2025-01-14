<?php

namespace Modules\Development\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Development\Entities\Configuration;
class ConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach ($configurations as $configuration) {
            Configuration::firstOrCreate(
                ['configuration_key' => $configuration['configuration_key']], // Check for an existing configuration with this key
                $configuration // If not found, create a new record with this data
            );
        }
    }
}
