<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\ModuleEntity;
use Modules\Utilities\Entities\Module;
use Modules\Utilities\Entities\Entity;

class ModuleEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get module IDs or create them if they don't exist
        $crmModule = Module::firstOrCreate(['module_key' => 'crm'], ['name' => 'CRM', 'status' => 'active']);
        $surveyModule = Module::firstOrCreate(['module_key' => 'survey'], ['name' => 'Survey', 'status' => 'active']);
        $posModule = Module::firstOrCreate(['module_key' => 'pos'], ['name' => 'POS', 'status' => 'active']);
        $hrModule = Module::firstOrCreate(['module_key' => 'hr'], ['name' => 'HR', 'status' => 'active']);
        $eventsModule = Module::firstOrCreate(['module_key' => 'events'], ['name' => 'events', 'status' => 'inactive']);
        $cmsModule = Module::firstOrCreate(['module_key' => 'cms'], ['name' => 'cms', 'status' => 'inactive']);
        $smsMarketingModule = Module::firstOrCreate(['module_key' => 'sms_marketing'], ['name' => 'sms marketing', 'status' => 'inactive']);
        $emailMarketingModule = Module::firstOrCreate(['module_key' => 'email_marketing'], ['name' => 'email marketing', 'status' => 'inactive']);
        $socialMediaMarketingModule = Module::firstOrCreate(['module_key' => 'social_media_marketing'], ['name' => 'social media marketing', 'status' => 'inactive']);
        $eCommerceModule = Module::firstOrCreate(['module_key' => 'e_commerce'], ['name' => 'e-commerce', 'status' => 'inactive']);
        $liveAgentModule = Module::firstOrCreate(['module_key' => 'live_agent'], ['name' => 'live agent', 'status' => 'inactive']);
        $expensesModule = Module::firstOrCreate(['module_key' => 'expenses'], ['name' => 'expenses', 'status' => 'inactive']);
        $inventoryModule = Module::firstOrCreate(['module_key' => 'inventory'], ['name' => 'inventory', 'status' => 'inactive']);
        $accountingModule = Module::firstOrCreate(['module_key' => 'accounting'], ['name' => 'accounting', 'status' => 'inactive']);
        $eLearningModule = Module::firstOrCreate(['module_key' => 'e_learning'], ['name' => 'e-learning', 'status' => 'inactive']);
        $projectManagementModule = Module::firstOrCreate(['module_key' => 'project_management'], ['name' => 'project management', 'status' => 'inactive']);
        $timeManagementModule = Module::firstOrCreate(['module_key' => 'time_management'], ['name' => 'time management', 'status' => 'inactive']);

        // Get entity IDs or create them if they don't exist
        $entities = [];
        $entityNames = [
            // Core/Utility entities
            'User', 'Role', 'Permission', 'Customer', 'Configuration', 'EmailTemplate', 'EmailCredential', 'EmailLog', 'EmailGroup', 'EmailRecipient', 'EmailSubscriber', 'EmailCampaign', 'EmailAttachment', 'File', 'Folder', 'Country', 'Province', 'City', 'Town', 'Street', 'Language', 'Translation', 'Notification', 'Plan', 'Subscription', 'Tenant', 'TenantUser', 'TenantSetting', 'Category', 'Type', 'Industry', 'Tag', 'Currency', 'Module', 'Entity', 'Unit', 'StaticPage', 'StaticPageAttribute', 'ApiKey', 'Payment', 'PaymentMethod', 'Transaction', 'Feature', 'Usage', 'Release', 'Announcement', 'ModuleEntity',
            // CRM entities
            'Lead', 'Contact', 'Company', 'Opportunity', 'Activity', 'CrmNote', 'CrmFile', 'CrmPipelineStage', 'CrmAutomationRule', 'CrmWebhook', 'CrmImportJob',
            // Survey entities
            'Survey', 'SurveyQuestion', 'SurveyQuestionOption', 'SurveyQuestionTranslation', 'SurveyPage', 'SurveyResponse', 'SurveyAnswer', 'SurveyTemplate', 'SurveyTheme', 'SurveyShare', 'SurveyAutomationRule', 'SurveyWebhook',
            // POS entities
            'Product', 'Category', 'SubCategory', 'Tag', 'Type', 'Barcode', 'ProductStock', 'ProductWholesale', 'OfferPrice', 'Damaged',
            // HR entities
            'Employee', 'Department', 'Position', 'LeaveRequest', 'LeaveType', 'LeaveBalance', 'Attendance', 'Payroll', 'PayrollItem', 'EmployeeContract', 'EmployeeDocument', 'EmploymentHistory', 'PerformanceReview', 'PerformanceCycle', 'Goal', 'KeyResult', 'Feedback', 'OneOnOne', 'JobOpening', 'Candidate', 'Application', 'Interview', 'Offer', 'Holiday', 'Shift', 'WorkSchedule', 'PipelineStage'
        ];

        foreach ($entityNames as $entityName) {
            $entities[$entityName] = Entity::firstOrCreate(['entity_name' => $entityName], ['entity_path' => 'Modules\\' . ucfirst($entityName) . '\\Entities\\' . $entityName]);
        }

        $moduleEntities = [
            // CRM Module Entities
            ['module_id' => $crmModule->id, 'entity_id' => $entities['Lead']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['Contact']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['Company']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['Opportunity']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['Activity']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmNote']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmFile']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmPipelineStage']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmAutomationRule']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmWebhook']->id],
            ['module_id' => $crmModule->id, 'entity_id' => $entities['CrmImportJob']->id],

            // Survey Module Entities
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['Survey']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyQuestion']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyQuestionOption']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyQuestionTranslation']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyPage']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyResponse']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyAnswer']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyTemplate']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyTheme']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyShare']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyAutomationRule']->id],
            ['module_id' => $surveyModule->id, 'entity_id' => $entities['SurveyWebhook']->id],

            // POS Module Entities
            ['module_id' => $posModule->id, 'entity_id' => $entities['Product']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['Category']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['SubCategory']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['Tag']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['Type']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['Barcode']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['ProductStock']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['ProductWholesale']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['OfferPrice']->id],
            ['module_id' => $posModule->id, 'entity_id' => $entities['Damaged']->id],

            // HR Module Entities
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Employee']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Department']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Position']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['LeaveRequest']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['LeaveType']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['LeaveBalance']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Attendance']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Payroll']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['PayrollItem']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['EmployeeContract']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['EmployeeDocument']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['EmploymentHistory']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['PerformanceReview']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['PerformanceCycle']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Goal']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['KeyResult']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Feedback']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['OneOnOne']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['JobOpening']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Candidate']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Application']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Interview']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Offer']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Holiday']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['Shift']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['WorkSchedule']->id],
            ['module_id' => $hrModule->id, 'entity_id' => $entities['PipelineStage']->id],
        ];

        foreach ($moduleEntities as $moduleEntityData) {
            ModuleEntity::firstOrCreate(
                [
                    'module_id' => $moduleEntityData['module_id'],
                    'entity_id' => $moduleEntityData['entity_id']
                ],
                $moduleEntityData
            );
        }

        $this->command->info('Module entities seeded successfully!');
    }
}
