<?php

namespace Modules\Auth\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * Update user profile data
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function updateProfile(User $user, array $data): bool
    {
        try
        {
            // Update basic user information
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'username' => $data['username'] ?? $user->username,
                'email' => $data['email'] ?? $user->email,
                'language_id' => $data['language_id'] ?? $user->language_id,
                'country_id' => $data['country_id'] ?? $user->country_id,
            ]);

            // Update meta information
            $metaData = [
                'phone' => $data['phone'] ?? $user->meta('phone'),
                'address' => $data['address'] ?? $user->meta('address'),
                'gender' => $data['gender'] ?? $user->meta('gender'),
                'birthdate' => $data['birthdate'] ?? $user->meta('birthdate'),
                'timezone' => $data['timezone'] ?? $user->meta('timezone'),
                'home_street_1' => $data['home_street_1'] ?? $user->meta('home_street_1'),
                'home_street_2' => $data['home_street_2'] ?? $user->meta('home_street_2'),
                'home_building_number' => $data['home_building_number'] ?? $user->meta('home_building_number'),
                'home_landmark' => $data['home_landmark'] ?? $user->meta('home_landmark'),
            ];

            // Handle avatar if provided
            if (isset($data['avatar']))
            {
                $metaData['avatar'] = $this->handleAvatarUpdate($user, $data['avatar']);
            }
            elseif (isset($data['remove_avatar']) && $data['remove_avatar'])
            {
                $metaData['avatar'] = null;
            }

            $user->setMeta($metaData);

            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Profile update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user avatar
     *
     * @param User $user
     * @param mixed $avatar
     * @return bool
     */
    public function updateAvatar(User $user, $avatar): bool
    {
        try
        {
            $avatarPath = $this->handleAvatarUpdate($user, $avatar);
            $user->setMeta(['avatar' => $avatarPath]);
            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Avatar update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove user avatar
     *
     * @param User $user
     * @return bool
     */
    public function removeAvatar(User $user): bool
    {
        try
        {
            $currentAvatar = $user->meta('avatar');
            if ($currentAvatar && Storage::disk('public')->exists($currentAvatar))
            {
                Storage::disk('public')->delete($currentAvatar);
            }
            
            $user->setMeta(['avatar' => null]);
            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Avatar removal error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        try
        {
            $user->update(['password' => Hash::make($newPassword)]);
            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Password update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user preferences
     *
     * @param User $user
     * @param array $preferences
     * @return bool
     */
    public function updatePreferences(User $user, array $preferences): bool
    {
        try
        {
            $metaData = [
                'notifications_email' => $preferences['notifications_email'] ?? $user->meta('notifications_email'),
                'notifications_push' => $preferences['notifications_push'] ?? $user->meta('notifications_push'),
                'notifications_sms' => $preferences['notifications_sms'] ?? $user->meta('notifications_sms'),
                'theme_mode' => $preferences['theme_mode'] ?? $user->meta('theme_mode'),
                'date_format' => $preferences['date_format'] ?? $user->meta('date_format'),
                'time_format' => $preferences['time_format'] ?? $user->meta('time_format'),
                'timezone' => $preferences['timezone'] ?? $user->meta('timezone'),
            ];

            $user->setMeta($metaData);

            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Preferences update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle avatar file upload and storage
     *
     * @param User $user
     * @param mixed $avatar
     * @return string|null
     */
    private function handleAvatarUpdate(User $user, $avatar): ?string
    {
        if (!$avatar)
        {
            return null;
        }

        // Remove old avatar if exists
        $currentAvatar = $user->meta('avatar');
        if ($currentAvatar && Storage::disk('public')->exists($currentAvatar))
        {
            Storage::disk('public')->delete($currentAvatar);
        }

        // Store new avatar
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
        $path = $avatar->storeAs('avatars', $fileName, 'public');

        return $path;
    }
}
