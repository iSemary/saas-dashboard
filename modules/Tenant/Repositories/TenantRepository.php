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
        $rows = $this->model->query()->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.tenants.edit',
                    'landlord.tenants.destroy',
                    "tenants",
                    "tenant",
                    true
                );
            })
            ->rawColumns(['actions'])
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
}
