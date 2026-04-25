<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Module;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $modules = [
            [
                'module_key' => 'crm',
                'name' => 'CRM',
                'description' => 'Customer Relationship Management',
                'route' => '/dashboard/modules/crm',
                'icon' => 'crm.png',
                'slogan' => 'One call, one deal',
                'navigation' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/dashboard/modules/crm', 'icon' => 'LayoutDashboard'],
                    ['key' => 'companies', 'label' => 'Companies', 'route' => '/dashboard/modules/crm/companies', 'icon' => 'Building2'],
                    ['key' => 'contacts', 'label' => 'Contacts', 'route' => '/dashboard/modules/crm/contacts', 'icon' => 'Users'],
                    ['key' => 'deals', 'label' => 'Deals', 'route' => '/dashboard/modules/crm/deals', 'icon' => 'Handshake'],
                ],
                'theme' => [
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#1E40AF',
                ],
                'status' => 'active',
            ],
            [
                'module_key' => 'survey',
                'name' => 'Survey',
                'description' => 'Create surveys, collect responses, and analyze results with advanced branching logic and real-time analytics',
                'route' => '/survey/',
                'icon' => 'FileText',
                'slogan' => 'Collect insights that drive decisions',
                'navigation' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/dashboard/modules/survey', 'icon' => 'LayoutDashboard'],
                    ['key' => 'surveys', 'label' => 'Surveys', 'route' => '/dashboard/modules/survey/surveys', 'icon' => 'FileText'],
                    ['key' => 'templates', 'label' => 'Templates', 'route' => '/dashboard/modules/survey/templates', 'icon' => 'Copy'],
                    ['key' => 'themes', 'label' => 'Themes', 'route' => '/dashboard/modules/survey/themes', 'icon' => 'Palette'],
                    ['key' => 'responses', 'label' => 'Responses', 'route' => '/dashboard/modules/survey/responses', 'icon' => 'MessageSquare'],
                    ['key' => 'analytics', 'label' => 'Analytics', 'route' => '/dashboard/modules/survey/analytics', 'icon' => 'BarChart3'],
                    ['key' => 'automation', 'label' => 'Automation', 'route' => '/dashboard/modules/survey/automation', 'icon' => 'Zap'],
                    ['key' => 'webhooks', 'label' => 'Webhooks', 'route' => '/dashboard/modules/survey/webhooks', 'icon' => 'Webhook'],
                ],
                'theme' => [
                    'primary_color' => '#8B5CF6',
                    'secondary_color' => '#5B21B6',
                ],
                'status' => 'active',
            ],
            [
                'module_key' => 'pos',
                'name' => 'POS',
                'description' => 'Point of Sale',
                'route' => '/dashboard/modules/pos',
                'icon' => 'pos.png',
                'slogan' => 'Sales made easy',
                'navigation' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/dashboard/modules/pos', 'icon' => 'LayoutDashboard'],
                    ['key' => 'products', 'label' => 'Products', 'route' => '/dashboard/modules/pos/products', 'icon' => 'Package'],
                    ['key' => 'orders', 'label' => 'Orders', 'route' => '/dashboard/modules/pos/orders', 'icon' => 'Receipt'],
                ],
                'theme' => [
                    'primary_color' => '#10B981',
                    'secondary_color' => '#047857',
                ],
                'status' => 'active',
            ],
            [
                'module_key' => 'hr',
                'name' => 'HR',
                'description' => 'Human Resources',
                'route' => '/dashboard/modules/hr',
                'icon' => 'hr.png',
                'slogan' => 'Managing human resources',
                'navigation' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/dashboard/modules/hr', 'icon' => 'LayoutDashboard'],
                    ['key' => 'departments', 'label' => 'Departments', 'route' => '/dashboard/modules/hr/departments', 'icon' => 'Building2'],
                    ['key' => 'employees', 'label' => 'Employees', 'route' => '/dashboard/modules/hr/employees', 'icon' => 'Users'],
                    ['key' => 'leave_requests', 'label' => 'Leave Requests', 'route' => '/dashboard/modules/hr/leave-requests', 'icon' => 'FileText'],
                ],
                'theme' => [
                    'primary_color' => '#F59E0B',
                    'secondary_color' => '#B45309',
                ],
                'status' => 'active',
            ],
            [
                'module_key' => 'events',
                'name' => 'events',
                'description' => 'Events is a good module',
                'route' => '/events/',
                'icon' => 'events.png',
                'slogan' => 'Create and manage events',
                'theme' => [
                    'primary_color' => '#EC4899',
                    'secondary_color' => '#9D174D',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'cms',
                'name' => 'cms',
                'description' => 'CMS is a good module',
                'route' => '/cms/',
                'icon' => 'cms.png',
                'slogan' => 'Manage your content',
                'theme' => [
                    'primary_color' => '#6366F1',
                    'secondary_color' => '#4338CA',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'sms_marketing',
                'name' => 'sms marketing',
                'description' => 'SMS Marketing is a good module',
                'route' => '/sms-marketing/',
                'icon' => 'sms_marketing.png',
                'slogan' => 'Reach customers directly',
                'theme' => [
                    'primary_color' => '#EF4444',
                    'secondary_color' => '#B91C1C',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'email_marketing',
                'name' => 'email marketing',
                'description' => 'Email Marketing is a good module',
                'route' => '/email-marketing/',
                'icon' => 'email_marketing.png',
                'slogan' => 'Engage through emails',
                'theme' => [
                    'primary_color' => '#14B8A6',
                    'secondary_color' => '#0F766E',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'social_media_marketing',
                'name' => 'social media marketing',
                'description' => 'Social Media Marketing is a good module',
                'route' => '/social-media-marketing/',
                'icon' => 'social_media_marketing.png',
                'slogan' => 'Grow your presence online',
                'theme' => [
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#1D4ED8',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'e_commerce',
                'name' => 'e-commerce',
                'description' => 'E-commerce is a good module',
                'route' => '/e-commerce/',
                'icon' => 'e_commerce.png',
                'slogan' => 'Sell your products online',
                'theme' => [
                    'primary_color' => '#F97316',
                    'secondary_color' => '#C2410C',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'live_agent',
                'name' => 'live agent',
                'description' => 'Live Agent is a good module',
                'route' => '/live-agent/',
                'icon' => 'live_agent.png',
                'slogan' => 'Live customer support',
                'theme' => [
                    'primary_color' => '#06B6D4',
                    'secondary_color' => '#0E7490',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'expenses',
                'name' => 'expenses',
                'description' => 'Expenses is a good module',
                'route' => '/expenses/',
                'icon' => 'expenses.png',
                'slogan' => 'Track your expenses',
                'theme' => [
                    'primary_color' => '#84CC16',
                    'secondary_color' => '#4D7C0F',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'inventory',
                'name' => 'inventory',
                'description' => 'Inventory is a good module',
                'route' => '/inventory/',
                'icon' => 'inventory.png',
                'slogan' => 'Manage your stock',
                'theme' => [
                    'primary_color' => '#A855F7',
                    'secondary_color' => '#7E22CE',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'accounting',
                'name' => 'accounting',
                'description' => 'Accounting is a good module',
                'route' => '/accounting/',
                'icon' => 'accounting.png',
                'slogan' => 'Track your financials',
                'theme' => [
                    'primary_color' => '#22C55E',
                    'secondary_color' => '#15803D',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'e_learning',
                'name' => 'e-learning',
                'description' => 'E-learning is a good module',
                'route' => '/e-learning/',
                'icon' => 'e_learning.png',
                'slogan' => 'Learn and grow',
                'theme' => [
                    'primary_color' => '#EAB308',
                    'secondary_color' => '#A16207',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'project_management',
                'name' => 'project management',
                'description' => 'Project Management is a good module',
                'route' => '/project-management/',
                'icon' => 'project_management.png',
                'slogan' => 'Manage your projects efficiently',
                'theme' => [
                    'primary_color' => '#2563EB',
                    'secondary_color' => '#1E3A8A',
                ],
                'status' => 'inactive',
            ],
            [
                'module_key' => 'time_management',
                'name' => 'time management',
                'description' => 'Time Management is a good module',
                'route' => '/time-management/',
                'icon' => 'time_management.png',
                'slogan' => 'Master your time',
                'theme' => [
                    'primary_color' => '#D946EF',
                    'secondary_color' => '#A21CAF',
                ],
                'status' => 'inactive',
            ],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['module_key' => $module['module_key']], // Check for an existing module with this key
                $module // If not found, create a new record with this data
            );
        }
    }
}
