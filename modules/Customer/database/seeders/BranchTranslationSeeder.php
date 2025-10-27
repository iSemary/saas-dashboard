<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\Language;

class BranchTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        if ($languages->isEmpty()) {
            $this->command->warn('No languages found. Skipping branch translation seeding.');
            return;
        }

        $translations = [
            // Branch specific translations
            [
                'translation_key' => 'branches',
                'translations' => [
                    'en' => 'Branches',
                    'ar' => 'الفروع',
                    'de' => 'Zweige',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch',
                'translations' => [
                    'en' => 'Branch',
                    'ar' => 'فرع',
                    'de' => 'Zweig',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_name',
                'translations' => [
                    'en' => 'Branch Name',
                    'ar' => 'اسم الفرع',
                    'de' => 'Zweigname',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_code',
                'translations' => [
                    'en' => 'Branch Code',
                    'ar' => 'رمز الفرع',
                    'de' => 'Zweigcode',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'manager_name',
                'translations' => [
                    'en' => 'Manager Name',
                    'ar' => 'اسم المدير',
                    'de' => 'Manager-Name',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'manager_email',
                'translations' => [
                    'en' => 'Manager Email',
                    'ar' => 'بريد المدير الإلكتروني',
                    'de' => 'Manager-E-Mail',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'manager_phone',
                'translations' => [
                    'en' => 'Manager Phone',
                    'ar' => 'هاتف المدير',
                    'de' => 'Manager-Telefon',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'city',
                'translations' => [
                    'en' => 'City',
                    'ar' => 'المدينة',
                    'de' => 'Stadt',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'state',
                'translations' => [
                    'en' => 'State',
                    'ar' => 'الولاية',
                    'de' => 'Bundesland',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'country',
                'translations' => [
                    'en' => 'Country',
                    'ar' => 'البلد',
                    'de' => 'Land',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'postal_code',
                'translations' => [
                    'en' => 'Postal Code',
                    'ar' => 'الرمز البريدي',
                    'de' => 'Postleitzahl',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'latitude',
                'translations' => [
                    'en' => 'Latitude',
                    'ar' => 'خط العرض',
                    'de' => 'Breitengrad',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'longitude',
                'translations' => [
                    'en' => 'Longitude',
                    'ar' => 'خط الطول',
                    'de' => 'Längengrad',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'website',
                'translations' => [
                    'en' => 'Website',
                    'ar' => 'الموقع الإلكتروني',
                    'de' => 'Website',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'location',
                'translations' => [
                    'en' => 'Location',
                    'ar' => 'الموقع',
                    'de' => 'Standort',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'manager',
                'translations' => [
                    'en' => 'Manager',
                    'ar' => 'المدير',
                    'de' => 'Manager',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'brand',
                'translations' => [
                    'en' => 'Brand',
                    'ar' => 'العلامة التجارية',
                    'de' => 'Marke',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'code',
                'translations' => [
                    'en' => 'Code',
                    'ar' => 'الرمز',
                    'de' => 'Code',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'suspended',
                'translations' => [
                    'en' => 'Suspended',
                    'ar' => 'معلق',
                    'de' => 'Ausgesetzt',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'address_information',
                'translations' => [
                    'en' => 'Address Information',
                    'ar' => 'معلومات العنوان',
                    'de' => 'Adressinformationen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_branch_name',
                'translations' => [
                    'en' => 'Enter branch name',
                    'ar' => 'أدخل اسم الفرع',
                    'de' => 'Zweigname eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_branch_code',
                'translations' => [
                    'en' => 'Enter branch code',
                    'ar' => 'أدخل رمز الفرع',
                    'de' => 'Zweigcode eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'leave_empty_to_auto_generate',
                'translations' => [
                    'en' => 'Leave empty to auto-generate',
                    'ar' => 'اتركه فارغاً للإنشاء التلقائي',
                    'de' => 'Leer lassen für automatische Generierung',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'select_brand',
                'translations' => [
                    'en' => 'Select brand',
                    'ar' => 'اختر العلامة التجارية',
                    'de' => 'Marke auswählen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_branch_description',
                'translations' => [
                    'en' => 'Enter branch description',
                    'ar' => 'أدخل وصف الفرع',
                    'de' => 'Zweigbeschreibung eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_phone_number',
                'translations' => [
                    'en' => 'Enter phone number',
                    'ar' => 'أدخل رقم الهاتف',
                    'de' => 'Telefonnummer eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_email_address',
                'translations' => [
                    'en' => 'Enter email address',
                    'ar' => 'أدخل عنوان البريد الإلكتروني',
                    'de' => 'E-Mail-Adresse eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_website_url',
                'translations' => [
                    'en' => 'Enter website URL',
                    'ar' => 'أدخل رابط الموقع الإلكتروني',
                    'de' => 'Website-URL eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_manager_name',
                'translations' => [
                    'en' => 'Enter manager name',
                    'ar' => 'أدخل اسم المدير',
                    'de' => 'Manager-Name eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_manager_email',
                'translations' => [
                    'en' => 'Enter manager email',
                    'ar' => 'أدخل بريد المدير الإلكتروني',
                    'de' => 'Manager-E-Mail eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_manager_phone',
                'translations' => [
                    'en' => 'Enter manager phone',
                    'ar' => 'أدخل هاتف المدير',
                    'de' => 'Manager-Telefon eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_address',
                'translations' => [
                    'en' => 'Enter address',
                    'ar' => 'أدخل العنوان',
                    'de' => 'Adresse eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_city',
                'translations' => [
                    'en' => 'Enter city',
                    'ar' => 'أدخل المدينة',
                    'de' => 'Stadt eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_state',
                'translations' => [
                    'en' => 'Enter state',
                    'ar' => 'أدخل الولاية',
                    'de' => 'Bundesland eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_country',
                'translations' => [
                    'en' => 'Enter country',
                    'ar' => 'أدخل البلد',
                    'de' => 'Land eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_postal_code',
                'translations' => [
                    'en' => 'Enter postal code',
                    'ar' => 'أدخل الرمز البريدي',
                    'de' => 'Postleitzahl eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_latitude',
                'translations' => [
                    'en' => 'Enter latitude',
                    'ar' => 'أدخل خط العرض',
                    'de' => 'Breitengrad eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'enter_longitude',
                'translations' => [
                    'en' => 'Enter longitude',
                    'ar' => 'أدخل خط الطول',
                    'de' => 'Längengrad eingeben',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_created_successfully',
                'translations' => [
                    'en' => 'Branch created successfully',
                    'ar' => 'تم إنشاء الفرع بنجاح',
                    'de' => 'Zweig erfolgreich erstellt',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_updated_successfully',
                'translations' => [
                    'en' => 'Branch updated successfully',
                    'ar' => 'تم تحديث الفرع بنجاح',
                    'de' => 'Zweig erfolgreich aktualisiert',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_deleted_successfully',
                'translations' => [
                    'en' => 'Branch deleted successfully',
                    'ar' => 'تم حذف الفرع بنجاح',
                    'de' => 'Zweig erfolgreich gelöscht',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_restored_successfully',
                'translations' => [
                    'en' => 'Branch restored successfully',
                    'ar' => 'تم استعادة الفرع بنجاح',
                    'de' => 'Zweig erfolgreich wiederhergestellt',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_not_found',
                'translations' => [
                    'en' => 'Branch not found',
                    'ar' => 'الفرع غير موجود',
                    'de' => 'Zweig nicht gefunden',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_create_branch',
                'translations' => [
                    'en' => 'Failed to create branch',
                    'ar' => 'فشل في إنشاء الفرع',
                    'de' => 'Fehler beim Erstellen des Zweigs',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_update_branch',
                'translations' => [
                    'en' => 'Failed to update branch',
                    'ar' => 'فشل في تحديث الفرع',
                    'de' => 'Fehler beim Aktualisieren des Zweigs',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_delete_branch',
                'translations' => [
                    'en' => 'Failed to delete branch',
                    'ar' => 'فشل في حذف الفرع',
                    'de' => 'Fehler beim Löschen des Zweigs',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_restore_branch',
                'translations' => [
                    'en' => 'Failed to restore branch',
                    'ar' => 'فشل في استعادة الفرع',
                    'de' => 'Fehler beim Wiederherstellen des Zweigs',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_branch',
                'translations' => [
                    'en' => 'Failed to retrieve branch',
                    'ar' => 'فشل في استرجاع الفرع',
                    'de' => 'Fehler beim Abrufen des Zweigs',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_branches',
                'translations' => [
                    'en' => 'Failed to retrieve branches',
                    'ar' => 'فشل في استرجاع الفروع',
                    'de' => 'Fehler beim Abrufen der Zweige',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_search_branches',
                'translations' => [
                    'en' => 'Failed to search branches',
                    'ar' => 'فشل في البحث في الفروع',
                    'de' => 'Fehler beim Suchen der Zweige',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_branch_statistics',
                'translations' => [
                    'en' => 'Failed to retrieve branch statistics',
                    'ar' => 'فشل في استرجاع إحصائيات الفروع',
                    'de' => 'Fehler beim Abrufen der Zweigstatistiken',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_active_branches',
                'translations' => [
                    'en' => 'Failed to retrieve active branches',
                    'ar' => 'فشل في استرجاع الفروع النشطة',
                    'de' => 'Fehler beim Abrufen der aktiven Zweige',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_branches_by_location',
                'translations' => [
                    'en' => 'Failed to retrieve branches by location',
                    'ar' => 'فشل في استرجاع الفروع حسب الموقع',
                    'de' => 'Fehler beim Abrufen der Zweige nach Standort',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_retrieve_brand_branches',
                'translations' => [
                    'en' => 'Failed to retrieve brand branches',
                    'ar' => 'فشل في استرجاع فروع العلامة التجارية',
                    'de' => 'Fehler beim Abrufen der Markenzweige',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'search_query_is_required',
                'translations' => [
                    'en' => 'Search query is required',
                    'ar' => 'استعلام البحث مطلوب',
                    'de' => 'Suchanfrage ist erforderlich',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'validation_failed',
                'translations' => [
                    'en' => 'Validation failed',
                    'ar' => 'فشل التحقق',
                    'de' => 'Validierung fehlgeschlagen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'something_went_wrong',
                'translations' => [
                    'en' => 'Something went wrong',
                    'ar' => 'حدث خطأ ما',
                    'de' => 'Etwas ist schief gelaufen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            // Import related translations
            [
                'translation_key' => 'import_branches',
                'translations' => [
                    'en' => 'Import Branches',
                    'ar' => 'استيراد الفروع',
                    'de' => 'Zweige importieren',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'import_instructions',
                'translations' => [
                    'en' => 'Import Instructions',
                    'ar' => 'تعليمات الاستيراد',
                    'de' => 'Importanweisungen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'download_template_first',
                'translations' => [
                    'en' => 'Download the Excel template first',
                    'ar' => 'قم بتحميل قالب Excel أولاً',
                    'de' => 'Laden Sie zuerst die Excel-Vorlage herunter',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'fill_template_with_branch_data',
                'translations' => [
                    'en' => 'Fill the template with your branch data',
                    'ar' => 'املأ القالب ببيانات الفروع الخاصة بك',
                    'de' => 'Füllen Sie die Vorlage mit Ihren Zweigdaten aus',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'upload_filled_template',
                'translations' => [
                    'en' => 'Upload the filled template',
                    'ar' => 'قم بتحميل القالب المملوء',
                    'de' => 'Laden Sie die ausgefüllte Vorlage hoch',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'review_preview_before_import',
                'translations' => [
                    'en' => 'Review the preview before importing',
                    'ar' => 'راجع المعاينة قبل الاستيراد',
                    'de' => 'Überprüfen Sie die Vorschau vor dem Import',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'download_template',
                'translations' => [
                    'en' => 'Download Template',
                    'ar' => 'تحميل القالب',
                    'de' => 'Vorlage herunterladen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'download_excel_template_with_sample_data',
                'translations' => [
                    'en' => 'Download Excel template with sample data',
                    'ar' => 'تحميل قالب Excel مع بيانات عينة',
                    'de' => 'Excel-Vorlage mit Beispieldaten herunterladen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'required_fields',
                'translations' => [
                    'en' => 'Required Fields',
                    'ar' => 'الحقول المطلوبة',
                    'de' => 'Erforderliche Felder',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branch_name_required',
                'translations' => [
                    'en' => 'Branch name is required',
                    'ar' => 'اسم الفرع مطلوب',
                    'de' => 'Zweigname ist erforderlich',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'brand_id_required',
                'translations' => [
                    'en' => 'Brand ID is required',
                    'ar' => 'معرف العلامة التجارية مطلوب',
                    'de' => 'Marken-ID ist erforderlich',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'status_optional_default_active',
                'translations' => [
                    'en' => 'Status is optional (default: active)',
                    'ar' => 'الحالة اختيارية (افتراضي: نشط)',
                    'de' => 'Status ist optional (Standard: aktiv)',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'select_excel_file',
                'translations' => [
                    'en' => 'Select Excel File',
                    'ar' => 'اختر ملف Excel',
                    'de' => 'Excel-Datei auswählen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'choose_file',
                'translations' => [
                    'en' => 'Choose file',
                    'ar' => 'اختر ملف',
                    'de' => 'Datei auswählen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'supported_formats',
                'translations' => [
                    'en' => 'Supported formats',
                    'ar' => 'الصيغ المدعومة',
                    'de' => 'Unterstützte Formate',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'max_size',
                'translations' => [
                    'en' => 'Max size',
                    'ar' => 'الحد الأقصى للحجم',
                    'de' => 'Maximale Größe',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'upload_and_preview',
                'translations' => [
                    'en' => 'Upload and Preview',
                    'ar' => 'تحميل ومعاينة',
                    'de' => 'Hochladen und Vorschau',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'processing_file',
                'translations' => [
                    'en' => 'Processing file',
                    'ar' => 'معالجة الملف',
                    'de' => 'Datei wird verarbeitet',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'file_processed_successfully',
                'translations' => [
                    'en' => 'File processed successfully',
                    'ar' => 'تم معالجة الملف بنجاح',
                    'de' => 'Datei erfolgreich verarbeitet',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'upload_failed',
                'translations' => [
                    'en' => 'Upload failed',
                    'ar' => 'فشل التحميل',
                    'de' => 'Upload fehlgeschlagen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'preview_data',
                'translations' => [
                    'en' => 'Preview Data',
                    'ar' => 'معاينة البيانات',
                    'de' => 'Datenvorschau',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'row',
                'translations' => [
                    'en' => 'Row',
                    'ar' => 'الصف',
                    'de' => 'Zeile',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'valid_rows',
                'translations' => [
                    'en' => 'Valid Rows',
                    'ar' => 'الصفوف الصحيحة',
                    'de' => 'Gültige Zeilen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'invalid_rows',
                'translations' => [
                    'en' => 'Invalid Rows',
                    'ar' => 'الصفوف غير الصحيحة',
                    'de' => 'Ungültige Zeilen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'valid',
                'translations' => [
                    'en' => 'Valid',
                    'ar' => 'صحيح',
                    'de' => 'Gültig',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'invalid',
                'translations' => [
                    'en' => 'Invalid',
                    'ar' => 'غير صحيح',
                    'de' => 'Ungültig',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'reset',
                'translations' => [
                    'en' => 'Reset',
                    'ar' => 'إعادة تعيين',
                    'de' => 'Zurücksetzen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'confirm_import',
                'translations' => [
                    'en' => 'Confirm Import',
                    'ar' => 'تأكيد الاستيراد',
                    'de' => 'Import bestätigen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'no_valid_data',
                'translations' => [
                    'en' => 'No Valid Data',
                    'ar' => 'لا توجد بيانات صحيحة',
                    'de' => 'Keine gültigen Daten',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'no_valid_rows_to_import',
                'translations' => [
                    'en' => 'No valid rows to import',
                    'ar' => 'لا توجد صفوف صحيحة للاستيراد',
                    'de' => 'Keine gültigen Zeilen zum Importieren',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'are_you_sure_you_want_to_import',
                'translations' => [
                    'en' => 'Are you sure you want to import',
                    'ar' => 'هل أنت متأكد من أنك تريد استيراد',
                    'de' => 'Sind Sie sicher, dass Sie importieren möchten',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'yes_import',
                'translations' => [
                    'en' => 'Yes, Import',
                    'ar' => 'نعم، استيراد',
                    'de' => 'Ja, importieren',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'importing_data',
                'translations' => [
                    'en' => 'Importing data',
                    'ar' => 'استيراد البيانات',
                    'de' => 'Daten werden importiert',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'import_successful',
                'translations' => [
                    'en' => 'Import Successful',
                    'ar' => 'الاستيراد ناجح',
                    'de' => 'Import erfolgreich',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'import_failed',
                'translations' => [
                    'en' => 'Import Failed',
                    'ar' => 'فشل الاستيراد',
                    'de' => 'Import fehlgeschlagen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'branches_imported_successfully',
                'translations' => [
                    'en' => 'Branches imported successfully',
                    'ar' => 'تم استيراد الفروع بنجاح',
                    'de' => 'Zweige erfolgreich importiert',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'failed_to_download_template',
                'translations' => [
                    'en' => 'Failed to download template',
                    'ar' => 'فشل في تحميل القالب',
                    'de' => 'Fehler beim Herunterladen der Vorlage',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'back_to_branches',
                'translations' => [
                    'en' => 'Back to Branches',
                    'ar' => 'العودة إلى الفروع',
                    'de' => 'Zurück zu Zweigen',
                ],
                'translation_context' => 'branches',
                'is_shareable' => true,
            ],
        ];

        foreach ($translations as $translationData) {
            foreach ($translationData['translations'] as $locale => $value) {
                $language = $languages->where('locale', $locale)->first();
                if ($language) {
                    Translation::updateOrCreate(
                        [
                            'language_id' => $language->id,
                            'translation_key' => $translationData['translation_key'],
                        ],
                        [
                            'translation_value' => $value,
                            'translation_context' => $translationData['translation_context'],
                            'is_shareable' => $translationData['is_shareable'],
                        ]
                    );
                }
            }
        }

        $this->command->info('Branch translations seeded successfully!');
    }
}
