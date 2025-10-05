<?php

namespace Modules\Auth\Repositories;

interface SettingsRepositoryInterface
{
    /**
     * Get all settings for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserSettings(int $userId): array;

    /**
     * Update user settings
     *
     * @param int $userId
     * @param array $settings
     * @return bool
     */
    public function updateUserSettings(int $userId, array $settings): bool;

    /**
     * Get a specific setting value
     *
     * @param int $userId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getUserSetting(int $userId, string $key, $default = null);

    /**
     * Set a specific setting value
     *
     * @param int $userId
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setUserSetting(int $userId, string $key, $value): bool;
}
