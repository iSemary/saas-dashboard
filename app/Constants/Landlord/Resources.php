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
                'name' => 'languages',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'translations',
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
                'name' => 'plans',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'subscriptions',
                'actions' => ['read', 'create', 'update', 'delete']
            ],
            [
                'name' => 'settings',
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
        ];
    }
}
