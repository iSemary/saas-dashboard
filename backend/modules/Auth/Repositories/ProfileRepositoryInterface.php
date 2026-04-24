<?php

namespace Modules\Auth\Repositories;

use App\Models\User;

interface ProfileRepositoryInterface
{
    /**
     * Update user profile data
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function updateProfile(User $user, array $data): bool;

    /**
     * Update user avatar
     *
     * @param User $user
     * @param mixed $avatar
     * @return bool
     */
    public function updateAvatar(User $user, $avatar): bool;

    /**
     * Remove user avatar
     *
     * @param User $user
     * @return bool
     */
    public function removeAvatar(User $user): bool;

    /**
     * Update user password
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $newPassword): bool;

    /**
     * Update user preferences
     *
     * @param User $user
     * @param array $preferences
     * @return bool
     */
    public function updatePreferences(User $user, array $preferences): bool;
}
