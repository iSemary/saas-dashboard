<?php

namespace App\Constants\Landlord;

class Resources
{
    public static function getRoles()
    {
        return [
            [
                'name' => 'landlord',
                'guard_name' => 'web'
            ],
            [
                'name' => 'developer',
                'guard_name' => 'web'
            ],
            [
                'name' => 'marketing',
                'guard_name' => 'web'
            ],
            [
                'name' => 'sales',
                'guard_name' => 'web'
            ],
            [
                'name' => 'viewer',
                'guard_name' => 'web'
            ]
        ];
    }

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
                'name' => 'email_groups',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_campaigns',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_subscribers',
                'actions' => ['read', 'update']
            ],
            [
                'name' => 'email_recipients',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'email_credentials',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'emails',
                'actions' => ['send', 'resend', 'read']
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
                'name' => 'static_pages',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'ip_blacklists',
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
                'name' => 'system_status',
                'actions' => ['read']
            ],
            [
                'name' => 'backups',
                'actions' => ['read', 'download']
            ],
            [
                'name' => 'modules_flow',
                'actions' => ['read', 'update']
            ],
            [
                'name' => 'database_flow',
                'actions' => ['read', 'update']
            ],
            [
                'name' => 'documentation',
                'actions' => ['read']
            ],
            [
                'name' => 'brands',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ],
            [
                'name' => 'tenant_owners',
                'actions' => ['read', 'create', 'update', 'delete', 'restore']
            ]
        ];
    }

    public static function getPermissionGroups()
    {
        return [
            [
                'name' => 'user_management',
                'guard_name' => 'api',
                'description' => 'User and system user management permissions',
                'permissions' => [
                    'read.users', 'create.users', 'update.users', 'delete.users', 'restore.users',
                    'read.system_users', 'create.system_users', 'update.system_users', 'delete.system_users', 'restore.system_users',
                ],
            ],
            [
                'name' => 'role_permission_management',
                'guard_name' => 'api',
                'description' => 'Role and permission management',
                'permissions' => [
                    'read.roles', 'create.roles', 'update.roles', 'delete.roles', 'restore.roles',
                    'read.permissions', 'create.permissions', 'update.permissions', 'delete.permissions', 'restore.permissions',
                ],
            ],
            [
                'name' => 'tenant_management',
                'guard_name' => 'api',
                'description' => 'Tenant and client management',
                'permissions' => [
                    'read.tenants', 'create.tenants', 'update.tenants', 'delete.tenants', 'restore.tenants',
                    'read.clients', 'create.clients', 'update.clients', 'delete.clients', 'restore.clients',
                ],
            ],
            [
                'name' => 'content_management',
                'guard_name' => 'api',
                'description' => 'Content and localization management',
                'permissions' => [
                    'read.categories', 'create.categories', 'update.categories', 'delete.categories', 'restore.categories',
                    'read.tags', 'create.tags', 'update.tags', 'delete.tags', 'restore.tags',
                    'read.languages', 'create.languages', 'update.languages', 'delete.languages', 'restore.languages',
                    'read.translations', 'create.translations', 'update.translations', 'delete.translations', 'restore.translations',
                ],
            ],
            [
                'name' => 'email_management',
                'guard_name' => 'api',
                'description' => 'Email templates, campaigns, and groups',
                'permissions' => [
                    'read.email_templates', 'create.email_templates', 'update.email_templates', 'delete.email_templates', 'restore.email_templates',
                    'read.email_groups', 'create.email_groups', 'update.email_groups', 'delete.email_groups', 'restore.email_groups',
                    'read.email_campaigns', 'create.email_campaigns', 'update.email_campaigns', 'delete.email_campaigns', 'restore.email_campaigns',
                    'read.emails', 'send.emails', 'resend.emails',
                ],
            ],
            [
                'name' => 'subscription_management',
                'guard_name' => 'api',
                'description' => 'Plans, subscriptions, and payments',
                'permissions' => [
                    'read.plans', 'create.plans', 'update.plans', 'delete.plans', 'restore.plans',
                    'read.subscriptions', 'create.subscriptions', 'update.subscriptions', 'delete.subscriptions', 'restore.subscriptions',
                    'read.payment_methods', 'create.payment_methods', 'update.payment_methods', 'delete.payment_methods', 'restore.payment_methods',
                    'read.payment_logs', 'create.payment_logs', 'update.payment_logs', 'delete.payment_logs', 'restore.payment_logs',
                ],
            ],
            [
                'name' => 'system_monitoring',
                'guard_name' => 'api',
                'description' => 'System logs, activity, and monitoring',
                'permissions' => [
                    'read.activity_logs', 'create.activity_logs', 'update.activity_logs', 'delete.activity_logs', 'restore.activity_logs',
                    'read.login_attempts', 'create.login_attempts', 'update.login_attempts', 'delete.login_attempts', 'restore.login_attempts',
                    'read.telescope', 'read.log_viewer', 'read.horizon',
                ],
            ],
            [
                'name' => 'file_management',
                'guard_name' => 'api',
                'description' => 'File and media management',
                'permissions' => [
                    'read.files', 'create.files', 'update.files', 'delete.files', 'manage.files',
                ],
            ],
        ];
    }
}
