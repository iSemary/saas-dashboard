<?php

namespace Modules\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\Language;

class TranslationSeeder extends Seeder
{
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

        $translations = [
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

        $this->command->info('Translations seeded successfully!');
    }
}
