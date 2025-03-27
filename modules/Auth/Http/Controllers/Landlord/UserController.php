<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Helpers\PhoneNumberHelper;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Modules\Auth\Http\Requests\Landlord\ProfileRequest;
use Illuminate\Support\Facades\Hash;
use Modules\Geography\Services\CountryService;
use Modules\Localization\Entities\Language;
use Modules\Localization\Services\LanguageService;
use Session;

class UserController extends ApiController
{
    protected $languageService;
    protected $countryService;

    public function __construct(LanguageService $languageService, CountryService $countryService)
    {
        $this->languageService = $languageService;
        $this->countryService = $countryService;
    }

    public function profile()
    {
        $user = auth()->user();
        $languages = $this->languageService->getAll();
        $countries = $this->countryService->getAll();
        return view("landlord.auth.profile.index", ['user' => $user, 'languages' => $languages, 'countries' => $countries]);
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        switch ($validated['type']) {
            case 'general':
                $this->updateGeneralInfo($user, $validated);
                break;

            case 'security':
                $this->updateSecurity($user, $validated);
                break;

            case 'preferences':
                $this->updatePreferences($user, $validated);
                break;
        }

        return $this->return(200, 'Profile updated successfully', ['reload' => true]);
    }

    protected function updateGeneralInfo($user, array $data)
    {
        // Handle avatar removal
        if (isset($data['remove_avatar'])) {
            $user->setMeta(['avatar' => null]);
            return;
        }

        // Update basic user information
        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'language_id' => $data['language_id'] ?? null,
            'country_id' => $data['country_id'] ?? null,
        ]);

        // Update meta information
        $user->setMeta([
            'phone' => $data['phone'] ? PhoneNumberHelper::clean($data['phone']) : null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'home_street_1' => $data['home_street_1'] ?? null,
            'home_street_2' => $data['home_street_2'] ?? null,
            'home_building_number' => $data['home_building_number'] ?? null,
            'home_landmark' => $data['home_landmark'] ?? null,
        ]);

        $language = Language::where('id', $data['language_id'])->first();
        Session::put('language', $language);
        Carbon::setLocale($language->locale);
    }

    protected function updateSecurity($user, array $data)
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            return $this->return(400, translate('current_password_is_incorrect'));
        }

        $user->update(['password' => Hash::make($data['new_password'])]);

        // TODO send password changed email
    }

    protected function updatePreferences($user, array $data)
    {
        $themeMap = [
            '1' => 'light',
            '2' => 'dark',
            '3' => 'system'
        ];

        $user->setMeta([
            'theme_mode' => $themeMap[$data['theme_mode']] ?? 'system'
        ]);
    }
}
