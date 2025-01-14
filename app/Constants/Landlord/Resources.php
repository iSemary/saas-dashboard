<?php

namespace App\Constants\Landlord;

class Resources
{
    public static function getResources()
    {
        return [
            [
                'name' => 'users',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'clients',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'tenants',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'permissions',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'roles',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'categories',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'tags',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'languages',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'translations',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'currencies',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'modules',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'countries',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'cities',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'towns',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'provinces',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'streets',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'currencies',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'customers',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'email_templates',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'email_logs',
                'actions' => ['read', 'resend']
            ],
            [
                'name' => 'plans',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'subscriptions',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'payment_methods',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'payment_logs',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'settings',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'announcements',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'releases',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'modules',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'types',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'industries',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'units',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'telescope',
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
        ];
    }
}
