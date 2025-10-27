<?php

namespace App\Constants\Tenant;

class Resources
{
    public static function getResources()
    {
        return [
            // Core Business Modules
            'crm' => [
                'leads' => ['read', 'create', 'update', 'delete', 'convert'],
                'opportunities' => ['read', 'create', 'update', 'delete', 'close'],
                'contacts' => ['read', 'create', 'update', 'delete', 'merge'],
                'companies' => ['read', 'create', 'update', 'delete', 'merge'],
                'activities' => ['read', 'create', 'update', 'delete'],
                'pipeline' => ['read', 'creadminate', 'update', 'delete'],
            ],
            'sales' => [
                'quotations' => ['read', 'create', 'update', 'delete', 'send', 'convert'],
                'orders' => ['read', 'create', 'update', 'delete', 'confirm', 'cancel'],
                'invoices' => ['read', 'create', 'update', 'delete', 'send', 'pay'],
                'products' => ['read', 'create', 'update', 'delete', 'duplicate'],
                'pricelists' => ['read', 'create', 'update', 'delete'],
                'discounts' => ['read', 'create', 'update', 'delete'],
            ],
            'inventory' => [
                'products' => ['read', 'create', 'update', 'delete', 'duplicate'],
                'stock' => ['read', 'create', 'update', 'delete', 'adjust'],
                'warehouses' => ['read', 'create', 'update', 'delete'],
                'moves' => ['read', 'create', 'update', 'delete', 'validate'],
                'valuations' => ['read', 'create', 'update', 'delete'],
                'reorder_rules' => ['read', 'create', 'update', 'delete'],
            ],
            'accounting' => [
                'chart_of_accounts' => ['read', 'create', 'update', 'delete'],
                'journal_entries' => ['read', 'create', 'update', 'delete', 'post'],
                'reports' => ['read', 'create', 'update', 'delete', 'export'],
                'reconciliation' => ['read', 'create', 'update', 'delete', 'match'],
                'budgets' => ['read', 'create', 'update', 'delete'],
                'fiscal_years' => ['read', 'create', 'update', 'delete'],
            ],
            'hr' => [
                'employees' => ['read', 'create', 'update', 'delete', 'archive'],
                'attendance' => ['read', 'create', 'update', 'delete', 'approve'],
                'payroll' => ['read', 'create', 'update', 'delete', 'process'],
                'recruitment' => ['read', 'create', 'update', 'delete', 'hire'],
                'leave_management' => ['read', 'create', 'update', 'delete', 'approve'],
                'performance' => ['read', 'create', 'update', 'delete', 'review'],
            ],
            'project' => [
                'projects' => ['read', 'create', 'update', 'delete', 'archive'],
                'tasks' => ['read', 'create', 'update', 'delete', 'assign'],
                'timesheets' => ['read', 'create', 'update', 'delete', 'approve'],
                'milestones' => ['read', 'create', 'update', 'delete'],
                'resources' => ['read', 'create', 'update', 'delete'],
                'budgets' => ['read', 'create', 'update', 'delete'],
            ],
            'purchase' => [
                'vendors' => ['read', 'create', 'update', 'delete', 'merge'],
                'purchase_orders' => ['read', 'create', 'update', 'delete', 'confirm'],
                'receipts' => ['read', 'create', 'update', 'delete', 'validate'],
                'bills' => ['read', 'create', 'update', 'delete', 'pay'],
                'requests' => ['read', 'create', 'update', 'delete', 'approve'],
                'agreements' => ['read', 'create', 'update', 'delete'],
            ],
            'marketing' => [
                'campaigns' => ['read', 'create', 'update', 'delete', 'launch'],
                'leads' => ['read', 'create', 'update', 'delete', 'qualify'],
                'automation' => ['read', 'create', 'update', 'delete', 'activate'],
                'analytics' => ['read', 'create', 'update', 'delete', 'export'],
                'segments' => ['read', 'create', 'update', 'delete'],
                'templates' => ['read', 'create', 'update', 'delete'],
            ],
            'website' => [
                'pages' => ['read', 'create', 'update', 'delete', 'publish'],
                'blogs' => ['read', 'create', 'update', 'delete', 'publish'],
                'ecommerce' => ['read', 'create', 'update', 'delete', 'configure'],
                'forms' => ['read', 'create', 'update', 'delete', 'embed'],
                'themes' => ['read', 'create', 'update', 'delete', 'activate'],
                'seo' => ['read', 'create', 'update', 'delete'],
            ],
            
            // System Resources
            'users' => ['read', 'create', 'update', 'delete', 'invite'],
            'roles' => ['read', 'create', 'update', 'delete'],
            'permissions' => ['read', 'create', 'update', 'delete'],
            'settings' => ['read', 'create', 'update', 'delete'],
            'reports' => ['read', 'create', 'update', 'delete', 'export'],
            'dashboards' => ['read', 'create', 'update', 'delete', 'customize'],
            'workflows' => ['read', 'create', 'update', 'delete', 'activate'],
            'integrations' => ['read', 'create', 'update', 'delete', 'connect'],
            'notifications' => ['read', 'create', 'update', 'delete'],
            'files' => ['read', 'create', 'update', 'delete', 'share'],
        ];
    }

    public static function getModuleResources()
    {
        return [
            'crm' => 'Customer Relationship Management',
            'sales' => 'Sales Management',
            'inventory' => 'Inventory Management',
            'accounting' => 'Accounting & Finance',
            'hr' => 'Human Resources',
            'project' => 'Project Management',
            'purchase' => 'Purchase Management',
            'marketing' => 'Marketing Automation',
            'website' => 'Website & E-commerce',
        ];
    }

    public static function getDefaultPermissions()
    {
        return [
            'read' => 'View records',
            'create' => 'Create new records',
            'update' => 'Edit existing records',
            'delete' => 'Delete records',
            'restore' => 'Restore deleted records',
            'export' => 'Export data',
            'import' => 'Import data',
            'approve' => 'Approve requests',
            'reject' => 'Reject requests',
            'archive' => 'Archive records',
            'duplicate' => 'Duplicate records',
            'merge' => 'Merge records',
            'convert' => 'Convert records',
            'send' => 'Send communications',
            'pay' => 'Process payments',
            'confirm' => 'Confirm orders',
            'cancel' => 'Cancel orders',
            'validate' => 'Validate records',
            'adjust' => 'Adjust quantities',
            'post' => 'Post journal entries',
            'match' => 'Match transactions',
            'process' => 'Process payroll',
            'hire' => 'Hire candidates',
            'review' => 'Review performance',
            'assign' => 'Assign tasks',
            'launch' => 'Launch campaigns',
            'qualify' => 'Qualify leads',
            'activate' => 'Activate workflows',
            'publish' => 'Publish content',
            'configure' => 'Configure settings',
            'embed' => 'Embed forms',
            'connect' => 'Connect integrations',
            'share' => 'Share files',
            'customize' => 'Customize dashboards',
        ];
    }
}
