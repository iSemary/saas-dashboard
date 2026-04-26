<?php

namespace Modules\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\Language;

class TranslationSeeder extends Seeder
{
    /**
     * Load translations from per-module JSON files.
     *
     * @param \Illuminate\Support\Collection $languages
     * @return array
     */
    protected function loadModuleTranslations($languages): array
    {
        $translations = [];
        $modulesPath = base_path('modules');

        if (!is_dir($modulesPath)) {
            return $translations;
        }

        // Get all module directories
        $moduleDirs = glob($modulesPath . '/*', GLOB_ONLYDIR);

        foreach ($moduleDirs as $moduleDir) {
            $moduleName = basename($moduleDir);
            $langDir = $moduleDir . '/resources/lang';

            if (!is_dir($langDir)) {
                continue;
            }

            // Load JSON files for each locale
            foreach ($languages as $language) {
                $jsonFile = $langDir . '/' . $language->locale . '.json';

                if (file_exists($jsonFile)) {
                    $jsonTranslations = json_decode(file_get_contents($jsonFile), true);

                    if (is_array($jsonTranslations)) {
                        foreach ($jsonTranslations as $key => $value) {
                            // Skip if already added from another locale
                            $existingKey = array_search($key, array_column($translations, 'translation_key'));

                            if ($existingKey !== false) {
                                // Add translation for this locale to existing entry
                                $translations[$existingKey]['translations'][$language->locale] = $value;
                            } else {
                                // Create new translation entry
                                $translations[] = [
                                    'translation_key' => $key,
                                    'translations' => [$language->locale => $value],
                                    'translation_context' => 'module',
                                    'is_shareable' => true,
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $translations;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        if ($languages->isEmpty()) {
            $this->command->warn('No languages found. Skipping translation seeding.');
            return;
        }

        // First, load translations from per-module JSON files
        $moduleTranslations = $this->loadModuleTranslations($languages);

        // Then, merge with hardcoded translations (hardcoded take precedence for conflicts)
        $hardcodedTranslations = [
            // Common UI elements
            [
                'translation_key' => 'common.save',
                'translations' => [
                    'en' => 'Save',
                    'ar' => 'حفظ',
                    'de' => 'Speichern',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.cancel',
                'translations' => [
                    'en' => 'Cancel',
                    'ar' => 'إلغاء',
                    'de' => 'Abbrechen',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.delete',
                'translations' => [
                    'en' => 'Delete',
                    'ar' => 'حذف',
                    'de' => 'Löschen',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.edit',
                'translations' => [
                    'en' => 'Edit',
                    'ar' => 'تعديل',
                    'de' => 'Bearbeiten',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.create',
                'translations' => [
                    'en' => 'Create',
                    'ar' => 'إنشاء',
                    'de' => 'Erstellen',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.search',
                'translations' => [
                    'en' => 'Search',
                    'ar' => 'بحث',
                    'de' => 'Suchen',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.loading',
                'translations' => [
                    'en' => 'Loading...',
                    'ar' => 'جاري التحميل...',
                    'de' => 'Laden...',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.error',
                'translations' => [
                    'en' => 'Error',
                    'ar' => 'خطأ',
                    'de' => 'Fehler',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.success',
                'translations' => [
                    'en' => 'Success',
                    'ar' => 'نجح',
                    'de' => 'Erfolg',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'common.warning',
                'translations' => [
                    'en' => 'Warning',
                    'ar' => 'تحذير',
                    'de' => 'Warnung',
                ],
                'translation_context' => 'ui',
                'is_shareable' => true,
            ],

            // Navigation
            [
                'translation_key' => 'nav.dashboard',
                'translations' => [
                    'en' => 'Dashboard',
                    'ar' => 'لوحة التحكم',
                    'de' => 'Dashboard',
                ],
                'translation_context' => 'navigation',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'nav.users',
                'translations' => [
                    'en' => 'Users',
                    'ar' => 'المستخدمون',
                    'de' => 'Benutzer',
                ],
                'translation_context' => 'navigation',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'nav.settings',
                'translations' => [
                    'en' => 'Settings',
                    'ar' => 'الإعدادات',
                    'de' => 'Einstellungen',
                ],
                'translation_context' => 'navigation',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'nav.profile',
                'translations' => [
                    'en' => 'Profile',
                    'ar' => 'الملف الشخصي',
                    'de' => 'Profil',
                ],
                'translation_context' => 'navigation',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'nav.logout',
                'translations' => [
                    'en' => 'Logout',
                    'ar' => 'تسجيل الخروج',
                    'de' => 'Abmelden',
                ],
                'translation_context' => 'navigation',
                'is_shareable' => true,
            ],

            // Authentication
            [
                'translation_key' => 'auth.login',
                'translations' => [
                    'en' => 'Login',
                    'ar' => 'تسجيل الدخول',
                    'de' => 'Anmelden',
                ],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'auth.register',
                'translations' => [
                    'en' => 'Register',
                    'ar' => 'تسجيل',
                    'de' => 'Registrieren',
                ],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'auth.email',
                'translations' => [
                    'en' => 'Email',
                    'ar' => 'البريد الإلكتروني',
                    'de' => 'E-Mail',
                ],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'auth.password',
                'translations' => [
                    'en' => 'Password',
                    'ar' => 'كلمة المرور',
                    'de' => 'Passwort',
                ],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'auth.forgot_password',
                'translations' => [
                    'en' => 'Forgot Password?',
                    'ar' => 'نسيت كلمة المرور؟',
                    'de' => 'Passwort vergessen?',
                ],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],

            // Form labels
            [
                'translation_key' => 'form.name',
                'translations' => [
                    'en' => 'Name',
                    'ar' => 'الاسم',
                    'de' => 'Name',
                ],
                'translation_context' => 'forms',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'form.email',
                'translations' => [
                    'en' => 'Email Address',
                    'ar' => 'عنوان البريد الإلكتروني',
                    'de' => 'E-Mail-Adresse',
                ],
                'translation_context' => 'forms',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'form.phone',
                'translations' => [
                    'en' => 'Phone Number',
                    'ar' => 'رقم الهاتف',
                    'de' => 'Telefonnummer',
                ],
                'translation_context' => 'forms',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'form.address',
                'translations' => [
                    'en' => 'Address',
                    'ar' => 'العنوان',
                    'de' => 'Adresse',
                ],
                'translation_context' => 'forms',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'form.description',
                'translations' => [
                    'en' => 'Description',
                    'ar' => 'الوصف',
                    'de' => 'Beschreibung',
                ],
                'translation_context' => 'forms',
                'is_shareable' => true,
            ],

            // Messages
            [
                'translation_key' => 'message.created_successfully',
                'translations' => [
                    'en' => 'Created successfully',
                    'ar' => 'تم الإنشاء بنجاح',
                    'de' => 'Erfolgreich erstellt',
                ],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.updated_successfully',
                'translations' => [
                    'en' => 'Updated successfully',
                    'ar' => 'تم التحديث بنجاح',
                    'de' => 'Erfolgreich aktualisiert',
                ],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.deleted_successfully',
                'translations' => [
                    'en' => 'Deleted successfully',
                    'ar' => 'تم الحذف بنجاح',
                    'de' => 'Erfolgreich gelöscht',
                ],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.confirm_delete',
                'translations' => [
                    'en' => 'Are you sure you want to delete this item?',
                    'ar' => 'هل أنت متأكد من أنك تريد حذف هذا العنصر؟',
                    'de' => 'Sind Sie sicher, dass Sie dieses Element löschen möchten?',
                ],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.no_data_found',
                'translations' => [
                    'en' => 'No data found',
                    'ar' => 'لم يتم العثور على بيانات',
                    'de' => 'Keine Daten gefunden',
                ],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],

            // Status
            [
                'translation_key' => 'status.active',
                'translations' => [
                    'en' => 'Active',
                    'ar' => 'نشط',
                    'de' => 'Aktiv',
                ],
                'translation_context' => 'status',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'status.inactive',
                'translations' => [
                    'en' => 'Inactive',
                    'ar' => 'غير نشط',
                    'de' => 'Inaktiv',
                ],
                'translation_context' => 'status',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'status.pending',
                'translations' => [
                    'en' => 'Pending',
                    'ar' => 'في الانتظار',
                    'de' => 'Ausstehend',
                ],
                'translation_context' => 'status',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'status.completed',
                'translations' => [
                    'en' => 'Completed',
                    'ar' => 'مكتمل',
                    'de' => 'Abgeschlossen',
                ],
                'translation_context' => 'status',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'status.cancelled',
                'translations' => [
                    'en' => 'Cancelled',
                    'ar' => 'ملغي',
                    'de' => 'Abgebrochen',
                ],
                'translation_context' => 'status',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.reports',
                'translations' => ['en' => 'HR Reports', 'ar' => 'تقارير الموارد البشرية', 'de' => 'HR-Berichte'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.reports_subtitle',
                'translations' => ['en' => 'Headcount and operational HR analytics', 'ar' => 'تحليلات الموارد البشرية', 'de' => 'Personalanalysen und Kennzahlen'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.recruitment',
                'translations' => ['en' => 'Recruitment', 'ar' => 'التوظيف', 'de' => 'Recruiting'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.recruitment_subtitle',
                'translations' => ['en' => 'Manage hiring pipeline, interviews, and offers.', 'ar' => 'إدارة مسار التوظيف والمقابلات والعروض', 'de' => 'Bewerbungspipeline, Interviews und Angebote verwalten'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.onboarding',
                'translations' => ['en' => 'Onboarding', 'ar' => 'التهيئة الوظيفية', 'de' => 'Onboarding'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.training',
                'translations' => ['en' => 'Training', 'ar' => 'التدريب', 'de' => 'Schulungen'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.assets',
                'translations' => ['en' => 'Assets', 'ar' => 'الأصول', 'de' => 'Assets'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.expenses',
                'translations' => ['en' => 'Expenses', 'ar' => 'المصروفات', 'de' => 'Ausgaben'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.announcements',
                'translations' => ['en' => 'Announcements & Policies', 'ar' => 'الإعلانات والسياسات', 'de' => 'Ankündigungen und Richtlinien'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.me',
                'translations' => ['en' => 'My HR Profile', 'ar' => 'ملفي الوظيفي', 'de' => 'Mein HR-Profil'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.me_leaves',
                'translations' => ['en' => 'My Leaves', 'ar' => 'إجازاتي', 'de' => 'Meine Urlaube'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.me_attendance',
                'translations' => ['en' => 'My Attendance', 'ar' => 'حضوري', 'de' => 'Meine Anwesenheit'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'dashboard.hr.me_payroll',
                'translations' => ['en' => 'My Payroll', 'ar' => 'مسير رواتبي', 'de' => 'Meine Gehaltsabrechnung'],
                'translation_context' => 'dashboard',
                'is_shareable' => true,
            ],

            // Additional message keys
            [
                'translation_key' => 'message.operation_failed',
                'translations' => ['en' => 'Operation failed', 'ar' => 'فشلت العملية', 'de' => 'Vorgang fehlgeschlagen'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.validation_failed',
                'translations' => ['en' => 'Validation failed', 'ar' => 'فشل التحقق', 'de' => 'Validierung fehlgeschlagen'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.resource_not_found',
                'translations' => ['en' => 'Resource not found', 'ar' => 'المورد غير موجود', 'de' => 'Ressource nicht gefunden'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.action_completed',
                'translations' => ['en' => 'Action completed successfully', 'ar' => 'تمت العملية بنجاح', 'de' => 'Aktion erfolgreich abgeschlossen'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.webhook_created',
                'translations' => ['en' => 'Webhook created successfully', 'ar' => 'تم إنشاء الويب هوك بنجاح', 'de' => 'Webhook erfolgreich erstellt'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.webhook_updated',
                'translations' => ['en' => 'Webhook updated successfully', 'ar' => 'تم تحديث الويب هوك بنجاح', 'de' => 'Webhook erfolgreich aktualisiert'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.webhook_deleted',
                'translations' => ['en' => 'Webhook deleted successfully', 'ar' => 'تم حذف الويب هوك بنجاح', 'de' => 'Webhook erfolgreich gelöscht'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.webhook_test_sent',
                'translations' => ['en' => 'Test webhook sent', 'ar' => 'تم إرسال ويب هوك الاختبار', 'de' => 'Test-Webhook gesendet'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.backup_created',
                'translations' => ['en' => 'Backup created successfully', 'ar' => 'تم إنشاء النسخة الاحتياطية بنجاح', 'de' => 'Backup erfolgreich erstellt'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.backup_not_found',
                'translations' => ['en' => 'Backup not found', 'ar' => 'النسخة الاحتياطية غير موجودة', 'de' => 'Backup nicht gefunden'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.backup_restored',
                'translations' => ['en' => 'Backup restored successfully', 'ar' => 'تم استعادة النسخة الاحتياطية بنجاح', 'de' => 'Backup erfolgreich wiederhergestellt'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.import_success',
                'translations' => ['en' => 'Import completed successfully', 'ar' => 'تم الاستيراد بنجاح', 'de' => 'Import erfolgreich abgeschlossen'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.file_not_found',
                'translations' => ['en' => 'File not found', 'ar' => 'الملف غير موجود', 'de' => 'Datei nicht gefunden'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.email_sent',
                'translations' => ['en' => 'Email sent successfully', 'ar' => 'تم إرسال البريد الإلكتروني بنجاح', 'de' => 'E-Mail erfolgreich gesendet'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'message.restored_successfully',
                'translations' => ['en' => 'Restored successfully', 'ar' => 'تمت الاستعادة بنجاح', 'de' => 'Erfolgreich wiederhergestellt'],
                'translation_context' => 'messages',
                'is_shareable' => true,
            ],

            // Authentication
            [
                'translation_key' => 'auth.unauthenticated',
                'translations' => ['en' => 'Unauthenticated', 'ar' => 'غير مصادق', 'de' => 'Nicht authentifiziert'],
                'translation_context' => 'authentication',
                'is_shareable' => true,
            ],

            // Exceptions
            [
                'translation_key' => 'exception.failed_store_file',
                'translations' => ['en' => 'Failed to store the file', 'ar' => 'فشل تخزين الملف', 'de' => 'Datei konnte nicht gespeichert werden'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.cannot_transition_status',
                'translations' => ['en' => 'Cannot transition status from :from to :to', 'ar' => 'لا يمكن تغيير الحالة من :from إلى :to', 'de' => 'Status kann nicht von :from zu :to geändert werden'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.money_negative',
                'translations' => ['en' => 'Amount cannot be negative', 'ar' => 'لا يمكن أن يكون المبلغ سالباً', 'de' => 'Betrag darf nicht negativ sein'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.cannot_delete_with_associated',
                'translations' => ['en' => 'Cannot delete item with associated records', 'ar' => 'لا يمكن حذف العنصر مع السجلات المرتبطة', 'de' => 'Element mit zugehörigen Datensätzen kann nicht gelöscht werden'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.brand_not_found',
                'translations' => ['en' => 'Brand not found', 'ar' => 'العلامة التجارية غير موجودة', 'de' => 'Marke nicht gefunden'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.already_done',
                'translations' => ['en' => 'This action has already been completed', 'ar' => 'تم إجراء هذه العملية بالفعل', 'de' => 'Diese Aktion wurde bereits ausgeführt'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.not_available_as_addon',
                'translations' => ['en' => 'This module is not available as an add-on', 'ar' => 'هذه الوحدة غير متاحة كإضافة', 'de' => 'Dieses Modul ist nicht als Add-on verfügbar'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.no_active_subscription',
                'translations' => ['en' => 'No active subscription found', 'ar' => 'لم يتم العثور على اشتراك نشط', 'de' => 'Kein aktives Abonnement gefunden'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.question_required',
                'translations' => ['en' => 'This question is required', 'ar' => 'هذا السؤال مطلوب', 'de' => 'Diese Frage ist erforderlich'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
            [
                'translation_key' => 'exception.answer_pattern_mismatch',
                'translations' => ['en' => 'Answer does not match the required pattern', 'ar' => 'الإجابة لا تتطابق مع النمط المطلوب', 'de' => 'Antwort entspricht nicht dem erforderlichen Muster'],
                'translation_context' => 'exceptions',
                'is_shareable' => true,
            ],
        ];

        // Merge module translations with hardcoded ones (hardcoded take precedence)
        $allTranslations = array_merge($moduleTranslations, $hardcodedTranslations);

        foreach ($allTranslations as $translationData) {
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

        $this->command->info('Translations seeded successfully!');
    }
}
