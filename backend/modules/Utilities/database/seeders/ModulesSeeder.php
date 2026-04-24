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
                'name' => 'crm',
                'description' => 'CRM is a good module',
                'route' => '/crm/',
                'icon' => 'crm.png',
                'slogan' => 'One call, one deal',
                'status' => 'active',
            ],
            [
                'module_key' => 'survey',
                'name' => 'survey',
                'description' => 'Survey is a good module',
                'route' => '/survey/',
                'icon' => 'survey.png',
                'slogan' => 'Your opinion matters',
                'status' => 'active',
            ],
            [
                'module_key' => 'pos',
                'name' => 'pos',
                'description' => 'POS is a good module',
                'route' => '/pos/',
                'icon' => 'pos.png',
                'slogan' => 'Sales made easy',
                'status' => 'active',
            ],
            [
                'module_key' => 'hr',
                'name' => 'hr',
                'description' => 'HR is a good module',
                'route' => '/hr/',
                'icon' => 'hr.png',
                'slogan' => 'Managing human resources',
                'status' => 'active',
            ],
            [
                'module_key' => 'events',
                'name' => 'events',
                'description' => 'Events is a good module',
                'route' => '/events/',
                'icon' => 'events.png',
                'slogan' => 'Create and manage events',
                'status' => 'active',
            ],
            [
                'module_key' => 'cms',
                'name' => 'cms',
                'description' => 'CMS is a good module',
                'route' => '/cms/',
                'icon' => 'cms.png',
                'slogan' => 'Manage your content',
                'status' => 'active',
            ],
            [
                'module_key' => 'sms_marketing',
                'name' => 'sms marketing',
                'description' => 'SMS Marketing is a good module',
                'route' => '/sms-marketing/',
                'icon' => 'sms_marketing.png',
                'slogan' => 'Reach customers directly',
                'status' => 'active',
            ],
            [
                'module_key' => 'email_marketing',
                'name' => 'email marketing',
                'description' => 'Email Marketing is a good module',
                'route' => '/email-marketing/',
                'icon' => 'email_marketing.png',
                'slogan' => 'Engage through emails',
                'status' => 'active',
            ],
            [
                'module_key' => 'social_media_marketing',
                'name' => 'social media marketing',
                'description' => 'Social Media Marketing is a good module',
                'route' => '/social-media-marketing/',
                'icon' => 'social_media_marketing.png',
                'slogan' => 'Grow your presence online',
                'status' => 'active',
            ],
            [
                'module_key' => 'e_commerce',
                'name' => 'e-commerce',
                'description' => 'E-commerce is a good module',
                'route' => '/e-commerce/',
                'icon' => 'e_commerce.png',
                'slogan' => 'Sell your products online',
                'status' => 'active',
            ],
            [
                'module_key' => 'live_agent',
                'name' => 'live agent',
                'description' => 'Live Agent is a good module',
                'route' => '/live-agent/',
                'icon' => 'live_agent.png',
                'slogan' => 'Live customer support',
                'status' => 'active',
            ],
            [
                'module_key' => 'expenses',
                'name' => 'expenses',
                'description' => 'Expenses is a good module',
                'route' => '/expenses/',
                'icon' => 'expenses.png',
                'slogan' => 'Track your expenses',
                'status' => 'active',
            ],
            [
                'module_key' => 'inventory',
                'name' => 'inventory',
                'description' => 'Inventory is a good module',
                'route' => '/inventory/',
                'icon' => 'inventory.png',
                'slogan' => 'Manage your stock',
                'status' => 'active',
            ],
            [
                'module_key' => 'accounting',
                'name' => 'accounting',
                'description' => 'Accounting is a good module',
                'route' => '/accounting/',
                'icon' => 'accounting.png',
                'slogan' => 'Track your financials',
                'status' => 'active',
            ],
            [
                'module_key' => 'e_learning',
                'name' => 'e-learning',
                'description' => 'E-learning is a good module',
                'route' => '/e-learning/',
                'icon' => 'e_learning.png',
                'slogan' => 'Learn and grow',
                'status' => 'active',
            ],
            [
                'module_key' => 'project_management',
                'name' => 'project management',
                'description' => 'Project Management is a good module',
                'route' => '/project-management/',
                'icon' => 'project_management.png',
                'slogan' => 'Manage your projects efficiently',
                'status' => 'active',
            ],
            [
                'module_key' => 'time_management',
                'name' => 'time management',
                'description' => 'Time Management is a good module',
                'route' => '/time-management/',
                'icon' => 'time_management.png',
                'slogan' => 'Master your time',
                'status' => 'active',
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
