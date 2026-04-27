<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Import\ImportService;
use App\Services\Import\Adapters\BranchImportAdapter;
use App\Services\Import\Adapters\BrandImportAdapter;
use App\Services\Import\Adapters\GenericImportAdapter;
use App\Services\BulkAction\BulkActionService;
use App\Services\BulkAction\Actions\DeleteAction;
use App\Services\BulkAction\Actions\ActivateAction;
use App\Services\BulkAction\Actions\DeactivateAction;

class ImportBulkActionServiceProvider extends ServiceProvider
{
    /**
     * Entity configurations for import and bulk actions
     */
    protected array $entities = [
        // Landlord entities
        'branches' => [
            'model' => 'App\\Models\\Branch',
            'adapter' => BranchImportAdapter::class,
            'bulk_actions' => ['delete', 'activate', 'deactivate', 'export'],
            'required_fields' => ['name', 'brand_id'],
            'optional_fields' => ['code', 'email', 'phone', 'address', 'city', 'status'],
        ],
        'brands' => [
            'model' => 'App\\Models\\Brand',
            'adapter' => BrandImportAdapter::class,
            'bulk_actions' => ['delete', 'activate', 'deactivate', 'export'],
            'required_fields' => ['name'],
            'optional_fields' => ['code', 'email', 'phone', 'website', 'description', 'status'],
        ],
        'users' => [
            'model' => 'App\\Models\\User',
            'adapter' => null, // No import for users (security)
            'bulk_actions' => ['delete', 'activate', 'deactivate', 'export'],
        ],
        'tenants' => [
            'model' => 'App\\Models\\Tenant',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'activate', 'deactivate', 'export'],
            'required_fields' => ['name', 'email'],
            'optional_fields' => ['phone', 'address', 'domain', 'status'],
        ],
        'currencies' => [
            'model' => 'App\\Models\\Currency',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'code'],
            'optional_fields' => ['symbol', 'exchange_rate', 'is_active'],
        ],
        'countries' => [
            'model' => 'App\\Models\\Country',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'code'],
            'optional_fields' => ['phone_code', 'currency', 'is_active'],
        ],
        'provinces' => [
            'model' => 'App\\Models\\Province',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'country_id'],
            'optional_fields' => ['code', 'is_active'],
        ],
        'cities' => [
            'model' => 'App\\Models\\City',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'province_id'],
            'optional_fields' => ['code', 'is_active'],
        ],
        'categories' => [
            'model' => 'App\\Models\\Category',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name'],
            'optional_fields' => ['slug', 'description', 'parent_id', 'is_active'],
        ],
        'tags' => [
            'model' => 'App\\Models\\Tag',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name'],
            'optional_fields' => ['slug', 'color', 'description'],
        ],
        'types' => [
            'model' => 'App\\Models\\Type',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name'],
            'optional_fields' => ['slug', 'model_type', 'is_active'],
        ],
        'units' => [
            'model' => 'App\\Models\\Unit',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'code'],
            'optional_fields' => ['symbol', 'conversion_factor', 'is_active'],
        ],
        'industries' => [
            'model' => 'App\\Models\\Industry',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name'],
            'optional_fields' => ['code', 'description', 'is_active'],
        ],
        'releases' => [
            'model' => 'App\\Models\\Release',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['version'],
            'optional_fields' => ['notes', 'release_date', 'is_active'],
        ],
        'static-pages' => [
            'model' => 'App\\Models\\StaticPage',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['title', 'slug'],
            'optional_fields' => ['content', 'meta_title', 'meta_description', 'is_active'],
        ],
        'announcements' => [
            'model' => 'App\\Models\\Announcement',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['title'],
            'optional_fields' => ['content', 'start_date', 'end_date', 'is_active'],
        ],
        'roles' => [
            'model' => 'App\\Models\\Role',
            'adapter' => null,
            'bulk_actions' => ['delete', 'export'],
        ],
        'permissions' => [
            'model' => 'App\\Models\\Permission',
            'adapter' => null,
            'bulk_actions' => ['export'],
        ],
        'languages' => [
            'model' => 'App\\Models\\Language',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'code'],
            'optional_fields' => ['locale', 'is_active', 'is_default'],
        ],
        'email-templates' => [
            'model' => 'App\\Models\\EmailTemplate',
            'adapter' => GenericImportAdapter::class,
            'bulk_actions' => ['delete', 'export'],
            'required_fields' => ['name', 'subject'],
            'optional_fields' => ['body', 'is_active'],
        ],
        'plans' => [
            'model' => 'App\\Models\\Plan',
            'adapter' => null,
            'bulk_actions' => ['delete', 'export'],
        ],
        'subscriptions' => [
            'model' => 'App\\Models\\Subscription',
            'adapter' => null,
            'bulk_actions' => ['export'],
        ],
        // Tenant entities
        'tickets' => [
            'model' => 'App\\Models\\Ticket',
            'adapter' => null, // Complex entity, custom adapter needed
            'bulk_actions' => ['delete', 'assign', 'change_status', 'change_priority', 'export'],
        ],
    ];

    public function register(): void
    {
        // Register Import Service with all adapters
        $this->app->singleton(ImportService::class, function ($app) {
            $service = new ImportService();

            foreach ($this->entities as $entity => $config) {
                if ($config['adapter']) {
                    $adapter = $this->createAdapter($entity, $config);
                    $service->registerAdapter($entity, $adapter);
                }
            }

            return $service;
        });

        // Register Bulk Action Service with all actions
        $this->app->singleton(BulkActionService::class, function ($app) {
            $service = new BulkActionService();

            // Register standard actions
            $service->registerAction('delete', new DeleteAction());
            $service->registerAction('activate', new ActivateAction());
            $service->registerAction('deactivate', new DeactivateAction());

            // Register entity configurations
            foreach ($this->entities as $entity => $config) {
                $service->registerEntityConfig($entity, [
                    'model' => $config['model'],
                    'actions' => $config['bulk_actions'],
                ]);
            }

            return $service;
        });
    }

    /**
     * Create import adapter for entity
     */
    protected function createAdapter(string $entity, array $config): object
    {
        $adapterClass = $config['adapter'];

        if ($adapterClass === GenericImportAdapter::class) {
            return new GenericImportAdapter(
                $entity,
                $config['model'],
                [
                    'required' => $config['required_fields'] ?? ['name'],
                    'optional' => $config['optional_fields'] ?? [],
                    'labels' => $this->getFieldLabels($config),
                ],
                $config['unique_fields'] ?? ['name']
            );
        }

        return new $adapterClass();
    }

    /**
     * Get field labels for entity
     */
    protected function getFieldLabels(array $config): array
    {
        $labels = [];
        $allFields = array_merge(
            $config['required_fields'] ?? ['name'],
            $config['optional_fields'] ?? []
        );

        foreach ($allFields as $field) {
            $labels[$field] = ucwords(str_replace('_', ' ', $field));
        }

        return $labels;
    }

    public function boot(): void
    {
        //
    }
}
