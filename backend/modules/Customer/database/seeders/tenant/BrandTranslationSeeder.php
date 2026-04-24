<?php

namespace Modules\namespace Modules\Customer\Database\Seeders\tenant;\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;

class BrandTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $englishLanguage = Language::where('locale', 'en')->first();
        
        if (!$englishLanguage) {
            $this->command->warn('English language not found. Skipping brand translations.');
            return;
        }

        $translations = [
            // Brand related translations
            'brands' => 'Brands',
            'brand' => 'Brand',
            'add_brand' => 'Add Brand',
            'edit_brand' => 'Edit Brand',
            'brand_details' => 'Brand Details',
            'brand_created_successfully' => 'Brand created successfully',
            'brand_updated_successfully' => 'Brand updated successfully',
            'brand_deleted_successfully' => 'Brand deleted successfully',
            'brand_restored_successfully' => 'Brand restored successfully',
            'brand_not_found' => 'Brand not found',
            'brand_modules' => 'Brand Modules',
            'modules_retrieved_successfully' => 'Modules retrieved successfully',
            'brands_retrieved_successfully' => 'Brands retrieved successfully',
            'modules_assigned_successfully' => 'Modules assigned successfully',
            
            // Module related translations
            'modules' => 'Modules',
            'module' => 'Module',
            'view_modules' => 'View Modules',
            'no_modules_assigned' => 'No modules assigned',
            'error_loading_modules' => 'Error loading modules',
            'coming_soon' => 'Coming Soon',
            'module_coming_soon' => 'This module is coming soon',
            
            // Brand switcher translations
            'switch_brand' => 'Switch Brand',
            'select_brand' => 'Select Brand',
            
            // General translations
            'loading' => 'Loading',
            'error' => 'Error',
            'error_loading_brands' => 'Error loading brands',
            'no_brands_found' => 'No brands found',
            'validation_failed' => 'Validation failed',
            'something_went_wrong' => 'Something went wrong',
            
            // Status translations
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            
            // Action translations
            'view' => 'View',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'restore' => 'Restore',
            
            // Module Dashboard translations
            'hr_dashboard' => 'HR Dashboard',
            'crm_dashboard' => 'CRM Dashboard',
            'pos_dashboard' => 'POS Dashboard',
            'accounting_dashboard' => 'Accounting Dashboard',
            'sales_dashboard' => 'Sales Dashboard',
            'inventory_dashboard' => 'Inventory Dashboard',
            
            'welcome_to_hr_dashboard' => 'Welcome to HR Dashboard',
            'welcome_to_crm_dashboard' => 'Welcome to CRM Dashboard',
            'welcome_to_pos_dashboard' => 'Welcome to POS Dashboard',
            
            'manage_your_human_resources_efficiently' => 'Manage your human resources efficiently',
            'manage_your_customer_relationships_effectively' => 'Manage your customer relationships effectively',
            'manage_your_point_of_sale_operations' => 'Manage your point of sale operations',
            
            // HR specific translations
            'total_employees' => 'Total Employees',
            'active_projects' => 'Active Projects',
            'pending_requests' => 'Pending Requests',
            'attendance_rate' => 'Attendance Rate',
            'add_employee' => 'Add Employee',
            'manage_schedule' => 'Manage Schedule',
            'hr_settings' => 'HR Settings',
            
            // CRM specific translations
            'total_leads' => 'Total Leads',
            'conversion_rate' => 'Conversion Rate',
            'active_deals' => 'Active Deals',
            'add_lead' => 'Add Lead',
            'new_deal' => 'New Deal',
            'schedule_meeting' => 'Schedule Meeting',
            
            // POS specific translations
            'today_sales' => 'Today Sales',
            'total_transactions' => 'Total Transactions',
            'average_order' => 'Average Order',
            'active_terminals' => 'Active Terminals',
            'new_sale' => 'New Sale',
            'manage_inventory' => 'Manage Inventory',
            'process_refund' => 'Process Refund',
            'vs_yesterday' => 'vs Yesterday',
        ];

        foreach ($translations as $key => $value) {
            Translation::updateOrCreate(
                [
                    'language_id' => $englishLanguage->id,
                    'translation_key' => $key,
                ],
                [
                    'translation_value' => $value,
                    'translation_context' => 'brand_management',
                    'is_shareable' => true,
                ]
            );
        }

        $this->command->info('Brand translations seeded successfully.');
    }
}
