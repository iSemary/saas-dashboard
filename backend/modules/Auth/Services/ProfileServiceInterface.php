<?php

namespace Modules\Auth\Services;

use App\Models\User;

interface ProfileServiceInterface
{
    /**
     * Update user profile
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function update(User $user, array $data): array;

    /**
     * Validate current password for security updates
     *
     * @param User $user
     * @param string $currentPassword
     * @return bool
     */
    public function validateCurrentPassword(User $user, string $currentPassword): bool;
}
