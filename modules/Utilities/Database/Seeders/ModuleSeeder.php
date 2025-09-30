<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Module;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'module_key' => 'auth',
                'name' => 'Authentication',
                'description' => 'User authentication and authorization system',
                'route' => 'auth',
                'icon' => null, // Will be handled by FileHandler trait
                'slogan' => 'Secure access for everyone',
                'status' => 'active',
            ],
            [
                'module_key' => 'customer',
                'name' => 'Customer Management',
                'description' => 'Comprehensive customer relationship management',
                'route' => 'customers',
                'icon' => null,
                'slogan' => 'Build lasting relationships',
                'status' => 'active',
            ],
            [
                'module_key' => 'development',
                'name' => 'Development Tools',
                'description' => 'Development and debugging utilities',
                'route' => 'development',
                'icon' => null,
                'slogan' => 'Tools for developers',
                'status' => 'active',
            ],
            [
                'module_key' => 'email',
                'name' => 'Email Management',
                'description' => 'Email templates and communication system',
                'route' => 'emails',
                'icon' => null,
                'slogan' => 'Communicate effectively',
                'status' => 'active',
            ],
            [
                'module_key' => 'file-manager',
                'name' => 'File Manager',
                'description' => 'File upload, storage, and management system',
                'route' => 'files',
                'icon' => null,
                'slogan' => 'Organize your files',
                'status' => 'active',
            ],
            [
                'module_key' => 'geography',
                'name' => 'Geography',
                'description' => 'Geographic data and location services',
                'route' => 'geography',
                'icon' => null,
                'slogan' => 'Location-based services',
                'status' => 'active',
            ],
            [
                'module_key' => 'localization',
                'name' => 'Localization',
                'description' => 'Multi-language support and translation management',
                'route' => 'localization',
                'icon' => null,
                'slogan' => 'Speak your language',
                'status' => 'active',
            ],
            [
                'module_key' => 'notification',
                'name' => 'Notifications',
                'description' => 'Real-time notification system',
                'route' => 'notifications',
                'icon' => null,
                'slogan' => 'Stay informed',
                'status' => 'active',
            ],
            [
                'module_key' => 'payment',
                'name' => 'Payment Processing',
                'description' => 'Payment gateway integration and processing',
                'route' => 'payments',
                'icon' => null,
                'slogan' => 'Secure payments made easy',
                'status' => 'active',
            ],
            [
                'module_key' => 'subscription',
                'name' => 'Subscription Management',
                'description' => 'Subscription plans and billing management',
                'route' => 'subscriptions',
                'icon' => null,
                'slogan' => 'Flexible subscription plans',
                'status' => 'active',
            ],
            [
                'module_key' => 'tenant',
                'name' => 'Tenant Management',
                'description' => 'Multi-tenant system management',
                'route' => 'tenants',
                'icon' => null,
                'slogan' => 'Multi-tenant architecture',
                'status' => 'active',
            ],
            [
                'module_key' => 'utilities',
                'name' => 'Utilities',
                'description' => 'Common utilities and helper functions',
                'route' => 'utilities',
                'icon' => null,
                'slogan' => 'Essential tools and utilities',
                'status' => 'active',
            ],
        ];

        foreach ($modules as $moduleData) {
            Module::create($moduleData);
        }

        $this->command->info('Modules seeded successfully!');
    }
}
