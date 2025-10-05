<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Carbon\Carbon;
use Modules\Auth\Repositories\ProfileRepositoryInterface;
use Modules\Localization\Entities\Language;
use Session;

class ProfileService implements ProfileServiceInterface
{
    protected $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Update user profile
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function update(User $user, array $data): array
    {
        $type = $data['type'] ?? 'general';

        try
        {
            switch ($type)
            {
                case 'general':
                    return $this->updateGeneralInfo($user, $data);

                case 'security':
                    return $this->updateSecurity($user, $data);

                case 'preferences':
                    return $this->updatePreferences($user, $data);

                default:
                    return [
                        'success' => false,
                        'message' => @translate('invalid_profile_type')
                    ];
            }
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_occurred') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate current password for security updates
     *
     * @param User $user
     * @param string $currentPassword
     * @return bool
     */
    public function validateCurrentPassword(User $user, string $currentPassword): bool
    {
        return \Hash::check($currentPassword, $user->password);
    }

    /**
     * Update general profile information
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    protected function updateGeneralInfo(User $user, array $data): array
    {
        if (!$this->profileRepository->updateProfile($user, $data))
        {
            return [
                'success' => false,
                'message' => @translate('failed_to_update_profile')
            ];
        }

        // Update language in session if changed
        if (isset($data['language_id']) && $data['language_id'])
        {
            $language = Language::where('id', $data['language_id'])->first();
            if ($language)
            {
                Session::put('language', $language);
                Carbon::setLocale($language->locale);
            }
        }

        return [
            'success' => true,
            'message' => @translate('profile_updated_successfully')
        ];
    }

    /**
     * Update security information (password)
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    protected function updateSecurity(User $user, array $data): array
    {
        // Validate current password
        if (!$this->validateCurrentPassword($user, $data['current_password']))
        {
            return [
                'success' => false,
                'message' => @translate('current_password_is_incorrect')
            ];
        }

        if (!$this->profileRepository->updatePassword($user, $data['new_password']))
        {
            return [
                'success' => false,
                'message' => @translate('failed_to_update_password')
            ];
        }

        // TODO: Send password changed email notification

        return [
            'success' => true,
            'message' => @translate('password_updated_successfully')
        ];
    }

    /**
     * Update user preferences
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    protected function updatePreferences(User $user, array $data): array
    {
        if (!$this->profileRepository->updatePreferences($user, $data))
        {
            return [
                'success' => false,
                'message' => @translate('failed_to_update_preferences')
            ];
        }

        // Update language in session if changed
        if (isset($data['language_id']) && $data['language_id'])
        {
            $language = Language::where('id', $data['language_id'])->first();
            if ($language)
            {
                Session::put('language', $language);
                Carbon::setLocale($language->locale);
            }
        }

        return [
            'success' => true,
            'message' => @translate('preferences_updated_successfully')
        ];
    }
}
