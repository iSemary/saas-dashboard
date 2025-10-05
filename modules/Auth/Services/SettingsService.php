<?php

namespace Modules\Auth\Services;

use Modules\Auth\Repositories\SettingsRepositoryInterface;

class SettingsService implements SettingsServiceInterface
{
    protected $settingsRepository;

    public function __construct(SettingsRepositoryInterface $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Get all user settings
     *
     * @param int $userId
     * @return array
     */
    public function getUserSettings(int $userId): array
    {
        try {
            $settings = $this->settingsRepository->getUserSettings($userId);
            
            return [
                'success' => true,
                'data' => $settings,
                'message' => @translate('settings_retrieved_successfully')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_retrieving_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update user settings
     *
     * @param int $userId
     * @param array $settingsData
     * @return array
     */
    public function updateUserSettings(int $userId, array $settingsData): array
    {
        try {
            // Validate settings data
            $validatedData = $this->validateSettingsData($settingsData);
            
            $result = $this->settingsRepository->updateUserSettings($userId, $validatedData);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => @translate('settings_updated_successfully'),
                    'data' => ['reload' => false]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => @translate('error_updating_settings')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get notification settings
     *
     * @param int $userId
     * @return array
     */
    public function getNotificationSettings(int $userId): array
    {
        try {
            $settings = $this->settingsRepository->getUserSettings($userId);
            
            $notificationSettings = [
                'notifications_email' => $settings['notifications_email'] ?? true,
                'notifications_push' => $settings['notifications_push'] ?? true,
                'notifications_sms' => $settings['notifications_sms'] ?? false,
                'email_frequency' => $settings['email_frequency'] ?? 'daily'
            ];
            
            return [
                'success' => true,
                'data' => $notificationSettings,
                'message' => @translate('notification_settings_retrieved')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_retrieving_notification_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update notification settings
     *
     * @param int $userId
     * @param array $notificationData
     * @return array
     */
    public function updateNotificationSettings(int $userId, array $notificationData): array
    {
        try {
            $validatedData = [
                'notifications_email' => filter_var($notificationData['notifications_email'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'notifications_push' => filter_var($notificationData['notifications_push'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'notifications_sms' => filter_var($notificationData['notifications_sms'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'email_frequency' => $notificationData['email_frequency'] ?? 'daily'
            ];
            
            $result = $this->settingsRepository->updateUserSettings($userId, $validatedData);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => @translate('notification_settings_updated'),
                    'data' => ['reload' => false]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => @translate('error_updating_notification_settings')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_notification_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get appearance settings
     *
     * @param int $userId
     * @return array
     */
    public function getAppearanceSettings(int $userId): array
    {
        try {
            $settings = $this->settingsRepository->getUserSettings($userId);
            
            $appearanceSettings = [
                'theme_mode' => $settings['theme_mode'] ?? 'light',
                'language_preference' => $settings['language_preference'] ?? 'en',
                'currency_preference' => $settings['currency_preference'] ?? 'USD',
                'timezone' => $settings['timezone'] ?? 'UTC',
                'date_format' => $settings['date_format'] ?? 'Y-m-d',
                'time_format' => $settings['time_format'] ?? '24'
            ];
            
            return [
                'success' => true,
                'data' => $appearanceSettings,
                'message' => @translate('appearance_settings_retrieved')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_retrieving_appearance_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update appearance settings
     *
     * @param int $userId
     * @param array $appearanceData
     * @return array
     */
    public function updateAppearanceSettings(int $userId, array $appearanceData): array
    {
        try {
            $validatedData = [
                'theme_mode' => in_array($appearanceData['theme_mode'] ?? 'light', ['light', 'dark']) 
                    ? $appearanceData['theme_mode'] : 'light',
                'language_preference' => $appearanceData['language_preference'] ?? 'en',
                'currency_preference' => $appearanceData['currency_preference'] ?? 'USD',
                'timezone' => $appearanceData['timezone'] ?? 'UTC',
                'date_format' => in_array($appearanceData['date_format'] ?? 'Y-m-d', ['Y-m-d', 'd-m-Y', 'm/d/Y']) 
                    ? $appearanceData['date_format'] : 'Y-m-d',
                'time_format' => in_array($appearanceData['time_format'] ?? '24', ['12', '24']) 
                    ? $appearanceData['time_format'] : '24'
            ];
            
            $result = $this->settingsRepository->updateUserSettings($userId, $validatedData);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => @translate('appearance_settings_updated'),
                    'data' => ['reload' => true] // Reload page to apply theme changes
                ];
            } else {
                return [
                    'success' => false,
                    'message' => @translate('error_updating_appearance_settings')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_appearance_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get privacy settings
     *
     * @param int $userId
     * @return array
     */
    public function getPrivacySettings(int $userId): array
    {
        try {
            $settings = $this->settingsRepository->getUserSettings($userId);
            
            $privacySettings = [
                'data_privacy_level' => $settings['data_privacy_level'] ?? 'standard',
                'profile_visibility' => $settings['profile_visibility'] ?? 'private',
                'allow_data_analytics' => filter_var($settings['allow_data_analytics'] ?? false, FILTER_VALIDATE_BOOLEAN)
            ];
            
            return [
                'success' => true,
                'data' => $privacySettings,
                'message' => @translate('privacy_settings_retrieved')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_retrieving_privacy_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update privacy settings
     *
     * @param int $userId
     * @param array $privacyData
     * @return array
     */
    public function updatePrivacySettings(int $userId, array $privacyData): array
    {
        try {
            $validatedData = [
                'data_privacy_level' => in_array($privacyData['data_privacy_level'] ?? 'standard', ['strict', 'standard', 'relaxed']) 
                    ? $privacyData['data_privacy_level'] : 'standard',
                'profile_visibility' => in_array($privacyData['profile_visibility'] ?? 'private', ['public', 'private', 'limited']) 
                    ? $privacyData['profile_visibility'] : 'private',
                'allow_data_analytics' => filter_var($privacyData['allow_data_analytics'] ?? false, FILTER_VALIDATE_BOOLEAN)
            ];
            
            $result = $this->settingsRepository->updateUserSettings($userId, $validatedData);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => @translate('privacy_settings_updated'),
                    'data' => ['reload' => false]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => @translate('error_updating_privacy_settings')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_privacy_settings') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate settings data
     *
     * @param array $settingsData
     * @return array
     */
    private function validateSettingsData(array $settingsData): array
    {
        $validatedData = [];

        // Notification settings
        if (isset($settingsData['notifications_email'])) {
            $validatedData['notifications_email'] = filter_var($settingsData['notifications_email'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($settingsData['notifications_push'])) {
            $validatedData['notifications_push'] = filter_var($settingsData['notifications_push'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($settingsData['notifications_sms'])) {
            $validatedData['notifications_sms'] = filter_var($settingsData['notifications_sms'], FILTER_VALIDATE_BOOLEAN);
        }

        // Appearance settings
        if (isset($settingsData['theme_mode']) && in_array($settingsData['theme_mode'], ['light', 'dark'])) {
            $validatedData['theme_mode'] = $settingsData['theme_mode'];
        }
        if (isset($settingsData['date_format']) && in_array($settingsData['date_format'], ['Y-m-d', 'd-m-Y', 'm/d/Y'])) {
            $validatedData['date_format'] = $settingsData['date_format'];
        }
        if (isset($settingsData['time_format']) && in_array($settingsData['time_format'], ['12', '24'])) {
            $validatedData['time_format'] = $settingsData['time_format'];
        }
        if (isset($settingsData['timezone'])) {
            $validatedData['timezone'] = $settingsData['timezone'];
        }
        if (isset($settingsData['language_preference'])) {
            $validatedData['language_preference'] = $settingsData['language_preference'];
        }
        if (isset($settingsData['currency_preference'])) {
            $validatedData['currency_preference'] = $settingsData['currency_preference'];
        }

        return $validatedData;
    }
}