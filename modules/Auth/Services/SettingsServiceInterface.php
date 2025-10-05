<?php

namespace Modules\Auth\Services;

interface SettingsServiceInterface
{
    /**
     * Get all user settings
     *
     * @param int $userId
     * @return array
     */
    public function getUserSettings(int $userId): array;

    /**
     * Update user settings
     *
     * @param int $userId
     * @param array $settingsData
     * @return array
     */
    public function updateUserSettings(int $userId, array $settingsData): array;

    /**
     * Get notification settings
     *
     * @param int $userId
     * @return array
     */
    public function getNotificationSettings(int $userId): array;

    /**
     * Update notification settings
     *
     * @param int $userId
     * @param array $notificationData
     * @return array
     */
    public function updateNotificationSettings(int $userId, array $notificationData): array;

    /**
     * Get appearance settings
     *
     * @param int $userId
     * @return array
     */
    public function getAppearanceSettings(int $userId): array;

    /**
     * Update appearance settings
     *
     * @param int $userId
     * @param array $appearanceData
     * @return array
     */
    public function updateAppearanceSettings(int $userId, array $appearanceData): array;

    /**
     * Get privacy settings
     *
     * @param int $userId
     * @return array
     */
    public function getPrivacySettings(int $userId): array;

    /**
     * Update privacy settings
     *
     * @param int $userId
     * @param array $privacyData
     * @return array
     */
    public function updatePrivacySettings(int $userId, array $privacyData): array;
}
