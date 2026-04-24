<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Http\Requests\Tenant\ProfileRequest;
use Modules\Auth\Services\ProfileServiceInterface;
use Modules\Geography\Services\CountryService;
use Modules\Localization\Entities\Language;
use Modules\Localization\Services\LanguageService;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;
use Session;

class ProfileController extends ApiController
{
    protected $profileService;
    protected $languageService;
    protected $countryService;

    public function __construct(
        ProfileServiceInterface $profileService,
        LanguageService $languageService,
        CountryService $countryService
    ) {
        $this->profileService = $profileService;
        $this->languageService = $languageService;
        $this->countryService = $countryService;
    }

    /**
     * Show tenant profile page
     */
    public function index()
    {
        $user = auth()->user();
        $tenant = Tenant::where('name', TenantHelper::getSubDomain())->first();
        $languages = $this->languageService->getAll();
        $countries = $this->countryService->getAll();
        $timezones = $this->countryService->getTimezones();

        return view('tenant.auth.profile.index', [
            'user' => $user,
            'tenant' => $tenant,
            'languages' => $languages,
            'countries' => $countries,
            'timezones' => $timezones,
            'title' => @translate('my_account')
        ]);
    }

    /**
     * Update tenant profile
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        try
        {
            $result = $this->profileService->update($user, $validated);

            if ($result['success'])
            {
                return $this->return(200, $result['message'], ['reload' => true]);
            }

            return $this->return(400, $result['message']);
        }
        catch (\Exception $e)
        {
            return $this->return(500, @translate('error_occurred') . ': ' . $e->getMessage());
        }
    }
}
