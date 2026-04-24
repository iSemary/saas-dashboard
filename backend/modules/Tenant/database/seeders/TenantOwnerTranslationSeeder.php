<?php

namespace Modules\Tenant\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;

class TenantOwnerTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'tenant_owners' => [
                'en' => 'Tenant Owners',
                'ar' => 'مالكو المستأجرين'
            ],
            'tenant_owner' => [
                'en' => 'Tenant Owner',
                'ar' => 'مالك المستأجر'
            ],
            'tenant_owner_management' => [
                'en' => 'Tenant Owner Management',
                'ar' => 'إدارة مالكي المستأجرين'
            ],
            'assign_user_to_tenant' => [
                'en' => 'Assign User to Tenant',
                'ar' => 'تعيين مستخدم للمستأجر'
            ],
            'user_assigned_successfully' => [
                'en' => 'User assigned to tenant successfully',
                'ar' => 'تم تعيين المستخدم للمستأجر بنجاح'
            ],
            'user_already_assigned' => [
                'en' => 'User is already assigned to this tenant',
                'ar' => 'المستخدم مُعيّن بالفعل لهذا المستأجر'
            ],
            'promote_to_super_admin' => [
                'en' => 'Promote to Super Admin',
                'ar' => 'ترقية إلى مدير عام'
            ],
            'demote_from_super_admin' => [
                'en' => 'Demote from Super Admin',
                'ar' => 'إلغاء ترقية من مدير عام'
            ],
            'super_admin' => [
                'en' => 'Super Admin',
                'ar' => 'مدير عام'
            ],
            'is_super_admin' => [
                'en' => 'Is Super Admin',
                'ar' => 'مدير عام'
            ],
            'tenant_owner_roles' => [
                'en' => 'Tenant Owner Roles',
                'ar' => 'أدوار مالكي المستأجرين'
            ],
            'owner' => [
                'en' => 'Owner',
                'ar' => 'مالك'
            ],
            'admin' => [
                'en' => 'Admin',
                'ar' => 'مدير'
            ],
            'manager' => [
                'en' => 'Manager',
                'ar' => 'مدير'
            ],
            'user' => [
                'en' => 'User',
                'ar' => 'مستخدم'
            ],
            'tenant_owner_permissions' => [
                'en' => 'Tenant Owner Permissions',
                'ar' => 'صلاحيات مالكي المستأجرين'
            ],
            'tenant_owner_status' => [
                'en' => 'Tenant Owner Status',
                'ar' => 'حالة مالك المستأجر'
            ],
            'active' => [
                'en' => 'Active',
                'ar' => 'نشط'
            ],
            'inactive' => [
                'en' => 'Inactive',
                'ar' => 'غير نشط'
            ],
            'suspended' => [
                'en' => 'Suspended',
                'ar' => 'معلق'
            ],
            'view_tenant_users' => [
                'en' => 'View Tenant Users',
                'ar' => 'عرض مستخدمي المستأجر'
            ],
            'tenant_users' => [
                'en' => 'Tenant Users',
                'ar' => 'مستخدمي المستأجر'
            ],
            'assigned_users' => [
                'en' => 'Assigned Users',
                'ar' => 'المستخدمون المعينون'
            ],
            'no_users_assigned' => [
                'en' => 'No users assigned to this tenant',
                'ar' => 'لا يوجد مستخدمون معينون لهذا المستأجر'
            ],
            'assign_new_user' => [
                'en' => 'Assign New User',
                'ar' => 'تعيين مستخدم جديد'
            ],
            'select_user' => [
                'en' => 'Select User',
                'ar' => 'اختر مستخدم'
            ],
            'select_role' => [
                'en' => 'Select Role',
                'ar' => 'اختر دور'
            ],
            'select_status' => [
                'en' => 'Select Status',
                'ar' => 'اختر الحالة'
            ],
            'update_permissions' => [
                'en' => 'Update Permissions',
                'ar' => 'تحديث الصلاحيات'
            ],
            'update_status' => [
                'en' => 'Update Status',
                'ar' => 'تحديث الحالة'
            ],
            'permissions_updated' => [
                'en' => 'Permissions updated successfully',
                'ar' => 'تم تحديث الصلاحيات بنجاح'
            ],
            'status_updated' => [
                'en' => 'Status updated successfully',
                'ar' => 'تم تحديث الحالة بنجاح'
            ],
            'user_promoted' => [
                'en' => 'User promoted to super admin successfully',
                'ar' => 'تم ترقية المستخدم إلى مدير عام بنجاح'
            ],
            'user_demoted' => [
                'en' => 'User demoted from super admin successfully',
                'ar' => 'تم إلغاء ترقية المستخدم من مدير عام بنجاح'
            ],
            'tenant_owner_created' => [
                'en' => 'Tenant owner created successfully',
                'ar' => 'تم إنشاء مالك المستأجر بنجاح'
            ],
            'tenant_owner_updated' => [
                'en' => 'Tenant owner updated successfully',
                'ar' => 'تم تحديث مالك المستأجر بنجاح'
            ],
            'tenant_owner_deleted' => [
                'en' => 'Tenant owner deleted successfully',
                'ar' => 'تم حذف مالك المستأجر بنجاح'
            ],
            'tenant_owner_restored' => [
                'en' => 'Tenant owner restored successfully',
                'ar' => 'تم استعادة مالك المستأجر بنجاح'
            ],
            'confirm_delete_tenant_owner' => [
                'en' => 'Are you sure you want to delete this tenant owner?',
                'ar' => 'هل أنت متأكد من حذف مالك المستأجر هذا؟'
            ],
            'confirm_remove_user' => [
                'en' => 'Are you sure you want to remove this user from the tenant?',
                'ar' => 'هل أنت متأكد من إزالة هذا المستخدم من المستأجر؟'
            ],
            'tenant_owner_statistics' => [
                'en' => 'Tenant Owner Statistics',
                'ar' => 'إحصائيات مالكي المستأجرين'
            ],
            'total_tenant_owners' => [
                'en' => 'Total Tenant Owners',
                'ar' => 'إجمالي مالكي المستأجرين'
            ],
            'active_tenant_owners' => [
                'en' => 'Active Tenant Owners',
                'ar' => 'مالكي المستأجرين النشطين'
            ],
            'inactive_tenant_owners' => [
                'en' => 'Inactive Tenant Owners',
                'ar' => 'مالكي المستأجرين غير النشطين'
            ],
            'suspended_tenant_owners' => [
                'en' => 'Suspended Tenant Owners',
                'ar' => 'مالكي المستأجرين المعلقين'
            ],
            'super_admins_count' => [
                'en' => 'Super Admins Count',
                'ar' => 'عدد المدراء العامين'
            ],
            'recent_assignments' => [
                'en' => 'Recent Assignments',
                'ar' => 'التعيينات الأخيرة'
            ],
            'by_tenant' => [
                'en' => 'By Tenant',
                'ar' => 'حسب المستأجر'
            ],
            'by_role' => [
                'en' => 'By Role',
                'ar' => 'حسب الدور'
            ],
        ];

        $languages = Language::all();

        foreach ($translations as $key => $translationsByLang) {
            foreach ($languages as $language) {
                $value = $translationsByLang[$language->locale] ?? $translationsByLang['en'];
                
                Translation::updateOrCreate(
                    [
                        'translation_key' => $key,
                        'language_id' => $language->id,
                    ],
                    [
                        'translation_value' => $value,
                    ]
                );
            }
        }

        $this->command->info('Tenant owner translations seeded successfully!');
    }
}
