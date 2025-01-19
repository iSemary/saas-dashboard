<?php

namespace App\Constants\Landlord;

class Resources
{
    public static function getResources()
    {
        return [
            [
                'name' => 'users',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'files',
                'actions' => ['read', 'create', 'update', 'delete', 'manage']
            ],
            [
                'name' => 'system_users',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'activity_logs',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'login_attempts',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'clients',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'tenants',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'permissions',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'roles',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'categories',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'tags',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'languages',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'translations',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'currencies',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'modules',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'countries',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'cities',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'towns',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'provinces',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'streets',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'currencies',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'customers',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_templates',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_campaigns',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_subscribers',
                'actions' => ['read']
            ],
            [
                'name' => 'email_recipients',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_logs',
                'actions' => ['read', 'resend']
            ],
            [
                'name' => 'emails',
                'actions' => ['send']
            ],
            [
                'name' => 'plans',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'subscriptions',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'payment_methods',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'payment_logs',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'configurations',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'announcements',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'releases',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'modules',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'types',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'industries',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'units',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'telescope',
                'actions' => ['read']
            ],
            [
                'name' => 'log_viewer',
                'actions' => ['read']
            ],
            [
                'name' => 'horizon',
                'actions' => ['read']
            ],
            [
                'name' => 'commands',
                'actions' => ['read']
            ],
            [
                'name' => 'code_builder',
                'actions' => ['read']
            ],
            [
                'name' => 'env_diff',
                'actions' => ['read']
            ],
            [
                'name' => 'modules_flow',
                'actions' => ['read', 'update']
            ],
            [
                'name' => 'database_flow',
                'actions' => ['read', 'update']
            ]
        ];
    }
}
