<?php

namespace Modules\Tenant\Repositories;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Helpers\TableHelper;
use Modules\Tenant\Entities\Tenant;
use Yajra\DataTables\DataTables;

class TenantRepository implements TenantInterface
{
    protected $model;

    public function __construct(Tenant $tenant)
    {
        $this->model = $tenant;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('table_count', function ($row) {
                try {
                    $dbName = $row->database;
                    $tableCount = $this->getTenantTableCount($dbName);
                    
                    // Determine expected count based on database type
                    $isLandlordDb = $dbName === 'saas_landlord' || str_contains($dbName, 'landlord');
                    $expectedCount = $isLandlordDb ? $this->getExpectedLandlordTableCount() : $this->getExpectedTableCount();
                    
                    $badgeClass = $tableCount >= $expectedCount ? 'bg-success' : 'bg-warning';
                    return '<span class="badge ' . $badgeClass . '">' . $tableCount . '/' . $expectedCount . '</span>';
                } catch (\Exception $e) {
                    return '<span class="badge bg-danger">Error</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                $actionButtons = TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.tenants.edit',
                    deleteRoute: 'landlord.tenants.destroy',
                    restoreRoute: 'landlord.tenants.restore',
                    type: "tenants",
                    titleType: "tenant",
                    showIconsOnly: true
                );

                // Add View Users button
                if (auth()->user()->hasPermissionTo('read.tenant_owners')) {
                    $actionButtons .= '<button type="button" title="' . translate("view_tenant_users") . '" data-modal-title="' . translate("tenant_users") . ' - ' . $row->name . '" data-modal-link="' . route('landlord.tenant-owners.by-tenant', $row->id) . '" class="btn-info mx-1 btn-sm open-details-btn">';
                    $actionButtons .= '<i class="fas fa-users"></i>';
                    $actionButtons .= '</button>';
                }

                // Add Database Management buttons
                if (auth()->user()->hasPermissionTo('manage.tenants')) {
                    // Re-migrate button
                    $actionButtons .= '<button type="button" title="Re-migrate Database" class="btn-warning mx-1 btn-sm tenant-remigrate" data-tenant-id="' . $row->id . '">';
                    $actionButtons .= '<i class="fas fa-database"></i>';
                    $actionButtons .= '</button>';

                    // Seed button
                    $actionButtons .= '<button type="button" title="Seed Database" class="btn-primary mx-1 btn-sm tenant-seed" data-tenant-id="' . $row->id . '">';
                    $actionButtons .= '<i class="fas fa-seedling"></i>';
                    $actionButtons .= '</button>';

                    // Re-seed button
                    $actionButtons .= '<button type="button" title="Re-seed Database" class="btn-secondary mx-1 btn-sm tenant-reseed" data-tenant-id="' . $row->id . '">';
                    $actionButtons .= '<i class="fas fa-redo"></i>';
                    $actionButtons .= '</button>';

                    // Monitoring button
                    $actionButtons .= '<button type="button" title="View Monitoring" class="btn-dark mx-1 btn-sm" onclick="window.open(\'' . route('landlord.monitoring.tenant', $row->id) . '\', \'_blank\')">';
                    $actionButtons .= '<i class="fas fa-chart-line"></i>';
                    $actionButtons .= '</button>';
                }

                return $actionButtons;
            })
            ->rawColumns(['actions', 'table_count'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            return $row;
        }
        return null;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
            return true;
        }
        return false;
    }

    /**
     * Initializes a new tenant with the given customer username.
     *
     * This method performs the following steps:
     * 1. Creates a tenant record for the given customer username.
     * 2. Sets up the database for the tenant.
     * 3. Migrates the tenant's database schema.
     * 4. Seeds the tenant's database with initial data.
     * 5. Creates a log database for the tenant in MongoDB.
     *
     * @param string $customerUsername The username of the customer for whom the tenant is being initialized.
     * @return array The details of the newly created tenant.
     */
    public function init(string $customerUsername)
    {
        $tenantId = $this->createTenantRecord($customerUsername);
        $this->setupDatabase($customerUsername);
        $this->migrateTenant($tenantId);
        $this->seedTenantDatabase($tenantId);
        $this->createLogMongoDatabase($customerUsername);

        // Return tenant details
        return $this->getTenantById($tenantId);
    }

    private function createTenantRecord($customerUsername)
    {
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => $customerUsername,
            'domain' => $this->generateDomain($customerUsername),
            'database' => $this->generateDatabaseName($customerUsername),
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        return $tenantId;
    }

    private function generateDomain($customerUsername)
    {
        return $customerUsername . '.' . config('settings.domain');
    }

    private function generateDatabaseName($customerUsername)
    {
        return config('settings.db_prefix') .  '_' . $customerUsername;
    }

    private function setupDatabase($customerUsername)
    {
        $dbName = $this->generateDatabaseName($customerUsername);
        $this->createDatabase($dbName);
    }

    private function createDatabase($dbName)
    {
        DB::statement("CREATE DATABASE IF NOT EXISTS " . $dbName);
    }

    private function migrateTenant($tenantId)
    {
        $paths = [
            'database/migrations/tenant',
            'modules/*/Database/Migrations/tenant',
            'modules/*/Database/migrations/tenant',
            'modules/*/Database/migrations/shared',
            'modules/*/Database/Migrations/shared',
        ];

        $database = 'tenant';

        foreach ($paths as $path) {
            $command = "tenants:artisan 'migrate --path={$path} --database={$database}' --tenant={$tenantId}";
            Artisan::call($command);
        }
    }

    private function seedTenantDatabase($tenantId)
    {
        $database = 'tenant';

        $command = "tenants:artisan 'migrate --database={$database} --seed' --tenant={$tenantId}";
        Artisan::call($command);
    }

    private function createLogMongoDatabase($customerUsername)
    {
        $databaseName = $this->generateDatabaseName($customerUsername);

        $clientOptions = ['authSource' => config('database.connections.logs.options.database')];

        // Check if username is defined in configuration
        if (!empty(config('database.connections.logs.username'))) {
            $clientOptions['username'] = config('database.connections.logs.username');
            $clientOptions['password'] = config('database.connections.logs.password');
        }

        $client = new \MongoDB\Client(
            "mongodb://" . config('database.connections.logs.host') . ":" . config('database.connections.logs.port'),
            $clientOptions
        );


        // Create database and a collection to ensure the database is created
        $db = $client->selectDatabase($databaseName);
        $db->createCollection('logs');
    }

    private function getTenantById($tenantId)
    {
        return DB::table('tenants')->where('id', $tenantId)->first();
    }

    /**
     * Get the number of tables in a tenant database
     */
    private function getTenantTableCount($dbName)
    {
        try {
            $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            return $tables[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the expected number of tables for a complete tenant setup
     */
    private function getExpectedTableCount()
    {
        // Count tenant migration files for accurate table count
        $tenantMigrations = 0;
        $paths = [
            'database/migrations/tenant',
            'modules/*/Database/Migrations/tenant',
            'modules/*/Database/migrations/tenant',
            'modules/*/Database/migrations/shared',
            'modules/*/Database/Migrations/shared',
        ];

        foreach ($paths as $path) {
            $migrationFiles = glob($path . '/*.php');
            if ($migrationFiles !== false) {
                $tenantMigrations += count($migrationFiles);
            }
        }

        return $tenantMigrations > 0 ? $tenantMigrations : 50; // Fallback if calculation fails
    }

    /**
     * Get the expected number of tables for a complete landlord setup
     */
    private function getExpectedLandlordTableCount()
    {
        // Count landlord migration files for accurate table count
        $landlordMigrations = 0;
        $paths = [
            'database/migrations',
            'modules/*/Database/Migrations/landlord',
            'modules/*/Database/migrations/landlord',
        ];

        foreach ($paths as $path) {
            $migrationFiles = glob($path . '/*.php');
            if ($migrationFiles !== false) {
                $landlordMigrations += count($migrationFiles);
            }
        }

        return $landlordMigrations > 0 ? $landlordMigrations : 78; // Fallback if calculation fails
    }

    /**
     * Re-migrate a tenant database
     */
    public function reMigrate($tenantId)
    {
        try {
            // Fresh migrate the tenant database
            $command = "tenants:artisan 'migrate:fresh --database=tenant' --tenant={$tenantId}";
            Artisan::call($command);

            // Re-run all migrations
            $this->migrateTenant($tenantId);

            return ['success' => true, 'message' => 'Database re-migrated successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Seed a tenant database
     */
    public function seedDatabase($tenantId)
    {
        try {
            $this->seedTenantDatabase($tenantId);
            return ['success' => true, 'message' => 'Database seeded successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Seeding failed: ' . $e->getMessage()];
        }
    }

    /**
     * Re-seed a tenant database (fresh seed)
     */
    public function reSeedDatabase($tenantId)
    {
        try {
            // Clear existing data and re-seed
            $command = "tenants:artisan 'migrate:fresh --seed --database=tenant' --tenant={$tenantId}";
            Artisan::call($command);

            return ['success' => true, 'message' => 'Database re-seeded successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Re-seeding failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get tenant database health information
     */
    public function getDatabaseHealth($tenantId)
    {
        $tenant = $this->find($tenantId);
        if (!$tenant) {
            return null;
        }

        try {
            $dbName = $tenant->database;
            
            // Get database size
            $sizeQuery = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);

            // Get table count
            $tableCount = $this->getTenantTableCount($dbName);
            $expectedCount = $this->getExpectedTableCount();

            // Get last backup timestamp (placeholder - implement based on your backup system)
            $lastBackup = null; // You can implement this based on your backup system

            return [
                'database_name' => $dbName,
                'size_mb' => $sizeQuery[0]->size_mb ?? 0,
                'table_count' => $tableCount,
                'expected_tables' => $expectedCount,
                'tables_complete' => $tableCount >= $expectedCount,
                'last_backup' => $lastBackup,
                'status' => $tableCount >= $expectedCount ? 'healthy' : 'incomplete'
            ];
        } catch (\Exception $e) {
            return [
                'database_name' => $tenant->database,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}
