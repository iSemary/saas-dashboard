<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $translations = [
            'brands' => [
                'en' => 'Brands',
                'ar' => 'العلامات التجارية'
            ],
            'brand' => [
                'en' => 'Brand',
                'ar' => 'العلامة التجارية'
            ],
            'brand_name' => [
                'en' => 'Brand Name',
                'ar' => 'اسم العلامة التجارية'
            ],
            'brand_slug' => [
                'en' => 'Brand Slug',
                'ar' => 'رابط العلامة التجارية'
            ],
            'brand_description' => [
                'en' => 'Brand Description',
                'ar' => 'وصف العلامة التجارية'
            ],
            'brand_logo' => [
                'en' => 'Brand Logo',
                'ar' => 'شعار العلامة التجارية'
            ],
            'create_brand' => [
                'en' => 'Create Brand',
                'ar' => 'إنشاء علامة تجارية'
            ],
            'edit_brand' => [
                'en' => 'Edit Brand',
                'ar' => 'تعديل العلامة التجارية'
            ],
            'brand_created_successfully' => [
                'en' => 'Brand created successfully',
                'ar' => 'تم إنشاء العلامة التجارية بنجاح'
            ],
            'brand_updated_successfully' => [
                'en' => 'Brand updated successfully',
                'ar' => 'تم تحديث العلامة التجارية بنجاح'
            ],
            'brand_deleted_successfully' => [
                'en' => 'Brand deleted successfully',
                'ar' => 'تم حذف العلامة التجارية بنجاح'
            ],
            'brand_restored_successfully' => [
                'en' => 'Brand restored successfully',
                'ar' => 'تم استعادة العلامة التجارية بنجاح'
            ],
            'brand_not_found' => [
                'en' => 'Brand not found',
                'ar' => 'العلامة التجارية غير موجودة'
            ],
            'brand_management' => [
                'en' => 'Brand Management',
                'ar' => 'إدارة العلامات التجارية'
            ],
            'brand_statistics' => [
                'en' => 'Brand Statistics',
                'ar' => 'إحصائيات العلامات التجارية'
            ],
            'total_brands' => [
                'en' => 'Total Brands',
                'ar' => 'إجمالي العلامات التجارية'
            ],
            'active_brands' => [
                'en' => 'Active Brands',
                'ar' => 'العلامات التجارية النشطة'
            ],
            'deleted_brands' => [
                'en' => 'Deleted Brands',
                'ar' => 'العلامات التجارية المحذوفة'
            ],
            'recent_brands' => [
                'en' => 'Recent Brands',
                'ar' => 'العلامات التجارية الحديثة'
            ],
            'brands_by_tenant' => [
                'en' => 'Brands by Tenant',
                'ar' => 'العلامات التجارية حسب المستأجر'
            ],
            'search_brands' => [
                'en' => 'Search Brands',
                'ar' => 'البحث في العلامات التجارية'
            ],
            'filter_by_tenant' => [
                'en' => 'Filter by Tenant',
                'ar' => 'تصفية حسب المستأجر'
            ],
            'filter_by_creator' => [
                'en' => 'Filter by Creator',
                'ar' => 'تصفية حسب المنشئ'
            ],
            'filter_by_date' => [
                'en' => 'Filter by Date',
                'ar' => 'تصفية حسب التاريخ'
            ],
            'brand_details' => [
                'en' => 'Brand Details',
                'ar' => 'تفاصيل العلامة التجارية'
            ],
            'brand_created_by' => [
                'en' => 'Created by',
                'ar' => 'تم الإنشاء بواسطة'
            ],
            'brand_updated_by' => [
                'en' => 'Updated by',
                'ar' => 'تم التحديث بواسطة'
            ],
            'brand_created_at' => [
                'en' => 'Created at',
                'ar' => 'تاريخ الإنشاء'
            ],
            'brand_updated_at' => [
                'en' => 'Updated at',
                'ar' => 'تاريخ التحديث'
            ],
            'upload_logo' => [
                'en' => 'Upload Logo',
                'ar' => 'رفع الشعار'
            ],
            'change_logo' => [
                'en' => 'Change Logo',
                'ar' => 'تغيير الشعار'
            ],
            'remove_logo' => [
                'en' => 'Remove Logo',
                'ar' => 'إزالة الشعار'
            ],
            'logo_preview' => [
                'en' => 'Logo Preview',
                'ar' => 'معاينة الشعار'
            ],
            'no_logo_uploaded' => [
                'en' => 'No logo uploaded',
                'ar' => 'لم يتم رفع شعار'
            ],
            'brand_slug_help' => [
                'en' => 'The slug will be used in URLs. Leave empty to auto-generate from name.',
                'ar' => 'سيتم استخدام الرابط في عناوين URL. اتركه فارغاً لإنشائه تلقائياً من الاسم.'
            ],
            'brand_description_help' => [
                'en' => 'Provide a detailed description of the brand and its values.',
                'ar' => 'قدم وصفاً مفصلاً للعلامة التجارية وقيمها.'
            ],
            'brand_tenant_help' => [
                'en' => 'Select the tenant that owns this brand.',
                'ar' => 'اختر المستأجر الذي يمتلك هذه العلامة التجارية.'
            ],
            'brand_validation_required' => [
                'en' => 'This field is required',
                'ar' => 'هذا الحقل مطلوب'
            ],
            'brand_validation_max' => [
                'en' => 'This field may not be greater than :max characters',
                'ar' => 'هذا الحقل قد لا يكون أكبر من :max حرف'
            ],
            'brand_validation_unique' => [
                'en' => 'This value has already been taken',
                'ar' => 'هذه القيمة مستخدمة بالفعل'
            ],
            'brand_validation_image' => [
                'en' => 'The file must be an image',
                'ar' => 'يجب أن يكون الملف صورة'
            ],
            'brand_validation_mimes' => [
                'en' => 'The file must be of type: :values',
                'ar' => 'يجب أن يكون الملف من النوع: :values'
            ],
            'brand_validation_max_size' => [
                'en' => 'The file may not be greater than :max kilobytes',
                'ar' => 'قد لا يكون الملف أكبر من :max كيلوبايت'
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

        $this->command->info('Brand translations seeded successfully!');
    }
}
