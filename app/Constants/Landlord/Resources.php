<?php

namespace App\Constants\Landlord;

class Resources
{
    public static function getResources()
    {
        return [
            [
                'name' => 'users',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'permissions',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'roles',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'categories',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'languages',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'translations',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'modules',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'countries',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'cities',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'towns',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'provinces',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'currencies',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'customers',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'email_templates',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'plans',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'subscriptions',
                'actions' => ['view', 'create', 'update', 'delete']
            ],
            [
                'name' => 'settings',
                'actions' => ['view', 'create', 'update', 'delete']
            ]
        ];
    }
}
