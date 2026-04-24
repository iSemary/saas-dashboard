<?php

namespace Modules\Auth\Repositories;

use Modules\Auth\Entities\User;

class SettingsRepository implements SettingsRepositoryInterface
{
    /**
     * Get all settings for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserSettings(int $userId): array
    {
        $user = User::find($userId);
        
        if (!$user) {
            return [];
        }

        $settings = [];
        
        // Get all meta keys that are considered settings
        $metaKeys = [
            'notifications_email',
            'notifications_push', 
            'notifications_sms',
            'theme_mode',
            'date_format',
            'time_format',
            'language_preference',
            'currency_preference',
            'timezone',
            'email_frequency',
            'data_privacy_level'
        ];

        foreach ($metaKeys as $key) {
            $value = $user->$key ?? null;
            if ($key === 'notifications_email' && $value === null) {
                $value = true; // Default to true
            }
            if ($key === 'notifications_push' && $value === null) {
                $value = true; // Default to true
            }
            if ($key === 'notifications_sms' && $value === null) {
                $value = false; // Default to false
            }
            if ($key === 'theme_mode' && $value === null) {
                $value = 'light'; // Default to light
            }
            if ($key === 'date_format' && $value === null) {
                $value = 'Y-m-d'; // Default format
            }
            if ($key === 'time_format' && $value === null) {
                $value = '24'; // Default to 24 hour
            }
            
            $settings[$key] = $value;
        }

        return $settings;
    }

    /**
     * Update user settings
     *
     * @param int $userId
     * @param array $settings
     * @return bool
     */
    public function updateUserSettings(int $userId, array $settings): bool
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return false;
            }

            // Update each setting
            foreach ($settings as $key => $value) {
                $user->$key = $value;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating user settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a specific setting value
     *
     * @param int $userId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getUserSetting(int $userId, string $key, $default = null)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $default;
        }

        $value = $user->$key ?? null;
        
        return $value !== null ? $value : $default;
    }

    /**
     * Set a specific setting value
     *
     * @param int $userId
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setUserSetting(int $userId, string $key, $value): bool
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return false;
            }

            $user->$key = $value;
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error setting user setting: ' . $e->getMessage());
            return false;
        }
    }
}
