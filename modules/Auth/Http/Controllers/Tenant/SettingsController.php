<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\ApiController;
use Modules\Auth\Http\Requests\Tenant\SettingsRequest;
use Modules\Auth\Services\SettingsServiceInterface;
use Modules\Localization\Entities\Language;
use Modules\Localization\Services\LanguageService;
use Modules\Geography\Services\CountryService;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class SettingsController extends ApiController
{
    protected $settingsService;
    protected $languageService;
    protected $countryService;

    public function __construct(
        SettingsServiceInterface $settingsService,
        LanguageService $languageService,
        CountryService $countryService
    ) {
        $this->settingsService = $settingsService;
        $this->languageService = $languageService;
        $this->countryService = $countryService;
    }

    /**
     * Show tenant settings page
     */
    public function index()
    {
        $user = auth()->user();
        $tenant = Tenant::where('name', TenantHelper::getSubDomain())->first();
        $languages = $this->languageService->getAll();
        $timezones = $this->countryService->getTimezones();
        
        // Get current user settings
        $settingsData = $this->settingsService->getUserSettings($user->id);
        $settings = $settingsData['success'] ? $settingsData['data'] : [];

        return view('tenant.auth.settings.index', [
            'user' => $user,
            'tenant' => $tenant,
            'languages' => $languages,
            'timezones' => $timezones,
            'settings' => $settings,
            'title' => 'Settings'
        ]);
    }

    /**
     * Update tenant settings
     */
    public function update(SettingsRequest $request)
    {
        $user = auth()->user();
        $section = $request->get('section', 'general');
        $validated = $request->validated();

        try {
            switch ($section) {
                case 'notifications':
                    $result = $this->settingsService->updateNotificationSettings($user->id, $validated);
                    break;
                case 'appearance':
                    $result = $this->settingsService->updateAppearanceSettings($user->id, $validated);
                    break;
                case 'privacy':
                    $result = $this->settingsService->updatePrivacySettings($user->id, $validated);
                    break;
                default:
                    $result = $this->settingsService->updateUserSettings($user->id, $validated);
                    break;
            }

            if ($result['success']) {
                return $this->return(200, $result['message'], $result['data'] ?? []);
            }

            return $this->return(400, $result['message']);
        } catch (\Exception $e) {
            return $this->return(500, 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings()
    {
        $user = auth()->user();
        $result = $this->settingsService->getNotificationSettings($user->id);
        
        if ($result['success']) {
            return $this->return(200, $result['message'], $result['data']);
        }
        
        return $this->return(400, $result['message']);
    }

    /**
     * Get appearance settings
     */
    public function getAppearanceSettings()
    {
        $user = auth()->user();
        $result = $this->settingsService->getAppearanceSettings($user->id);
        
        if ($result['success']) {
            return $this->return(200, $result['message'], $result['data']);
        }
        
        return $this->return(400, $result['message']);
    }

    /**
     * Get privacy settings
     */
    public function getPrivacySettings()
    {
        $user = auth()->user();
        $result = $this->settingsService->getPrivacySettings($user->id);
        
        if ($result['success']) {
            return $this->return(200, $result['message'], $result['data']);
        }
        
        return $this->return(400, $result['message']);
    }

    /**
     * Export user settings
     */
    public function export()
    {
        try {
            $user = auth()->user();
            $settings = $this->settingsService->getUserSettings($user->id);
            
            if ($settings['success']) {
                $filename = 'user_settings_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.json';
                
                return response()->json($settings['data'])
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Content-Type', 'application/json');
            }
            
            return $this->return(400, 'Error exporting settings');
        } catch (\Exception $e) {
            return $this->return(500, 'Error exporting settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to defaults
     */
    public function reset()
    {
        try {
            $user = auth()->user();
            
            // Default settings
            $defaultSettings = [
                'notifications_email' => true,
                'notifications_push' => true,
                'notifications_sms' => false,
                'theme_mode' => 'light',
                'date_format' => 'Y-m-d',
                'time_format' => '24',
                'data_privacy_level' => 'standard',
                'profile_visibility' => 'private',
                'allow_data_analytics' => false
            ];
            
            $result = $this->settingsService->updateUserSettings($user->id, $defaultSettings);
            
            if ($result['success']) {
                return $this->return(200, 'Settings reset to defaults', ['reload' => true]);
            }
            
            return $this->return(400, 'Error resetting settings');
        } catch (\Exception $e) {
            return $this->return(500, 'Error resetting settings: ' . $e->getMessage());
        }
    }
}
