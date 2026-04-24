<?php

namespace Modules\Development\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Development\Entities\DatabaseFlow;

class DatabaseFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $databaseFlows = [
            [
                'connection' => 'landlord',
                'table' => 'users',
                'position' => ['x' => 100, 'y' => 100],
                'color' => '#3B82F6',
            ],
            [
                'connection' => 'landlord',
                'table' => 'roles',
                'position' => ['x' => 300, 'y' => 100],
                'color' => '#10B981',
            ],
            [
                'connection' => 'landlord',
                'table' => 'permissions',
                'position' => ['x' => 500, 'y' => 100],
                'color' => '#F59E0B',
            ],
            [
                'connection' => 'landlord',
                'table' => 'customers',
                'position' => ['x' => 100, 'y' => 300],
                'color' => '#8B5CF6',
            ],
            [
                'connection' => 'landlord',
                'table' => 'categories',
                'position' => ['x' => 300, 'y' => 300],
                'color' => '#EF4444',
            ],
            [
                'connection' => 'landlord',
                'table' => 'currencies',
                'position' => ['x' => 500, 'y' => 300],
                'color' => '#06B6D4',
            ],
            [
                'connection' => 'landlord',
                'table' => 'industries',
                'position' => ['x' => 100, 'y' => 500],
                'color' => '#84CC16',
            ],
            [
                'connection' => 'landlord',
                'table' => 'tags',
                'position' => ['x' => 300, 'y' => 500],
                'color' => '#F97316',
            ],
            [
                'connection' => 'landlord',
                'table' => 'types',
                'position' => ['x' => 500, 'y' => 500],
                'color' => '#EC4899',
            ],
            [
                'connection' => 'landlord',
                'table' => 'units',
                'position' => ['x' => 100, 'y' => 700],
                'color' => '#6366F1',
            ],
            [
                'connection' => 'landlord',
                'table' => 'modules',
                'position' => ['x' => 300, 'y' => 700],
                'color' => '#14B8A6',
            ],
            [
                'connection' => 'landlord',
                'table' => 'releases',
                'position' => ['x' => 500, 'y' => 700],
                'color' => '#F59E0B',
            ],
            [
                'connection' => 'shared',
                'table' => 'files',
                'position' => ['x' => 700, 'y' => 100],
                'color' => '#8B5CF6',
            ],
            [
                'connection' => 'shared',
                'table' => 'folders',
                'position' => ['x' => 700, 'y' => 300],
                'color' => '#EF4444',
            ],
            [
                'connection' => 'shared',
                'table' => 'translations',
                'position' => ['x' => 700, 'y' => 500],
                'color' => '#06B6D4',
            ],
            [
                'connection' => 'shared',
                'table' => 'languages',
                'position' => ['x' => 700, 'y' => 700],
                'color' => '#84CC16',
            ],
        ];

        foreach ($databaseFlows as $flowData) {
            DatabaseFlow::create($flowData);
        }

        $this->command->info('Database flows seeded successfully!');
    }
}
