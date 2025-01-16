<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Tenant\Services\SystemUserService;
use Illuminate\Http\Request;
use Modules\Auth\Services\PermissionService;
use Modules\Geography\Services\CountryService;
use Modules\Localization\Services\LanguageService;

class SystemUserController extends ApiController
{
    protected $service;
    protected $permissionService;
    protected $countryService;
    protected $languageService;

    public function __construct(
        SystemUserService $service,
        PermissionService $permissionService,
        CountryService $countryService,
        LanguageService $languageService,
    ) {
        $this->service = $service;
        $this->permissionService = $permissionService;
        $this->countryService = $countryService;
        $this->languageService = $languageService;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate("system_users");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("system_users")],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate("system_user"),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.system-users.create'),
                    'data-modal-title' => translate("create") . " " . translate("system_user"),
                ]
            ],
        ];

        return view('landlord.tenant.system-users.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $permissions = $this->permissionService->getAll();
        $countries = $this->countryService->getAll();
        $languages = $this->languageService->getAll();
        return view('landlord.tenant.system-users.editor', compact('permissions', 'countries', 'languages'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, "Created successfully");
    }

    public function show($id) {}

    public function edit($id)
    {
        $permissions = $this->permissionService->getAll();
        $countries = $this->countryService->getAll();
        $languages = $this->languageService->getAll();
        $row = $this->service->get($id);
        return view('landlord.tenant.system-users.editor', compact('row', 'permissions', 'countries', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, "Updated successfully");
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }

    public function checkEmail(Request $request)
    {
        $email = $request->email;
        $id = $request->user_id;
        $result = $this->service->checkEmail($email, $id);
        return $this->return($result ? 400 : 200, $result ? translate("email_is_unavailable") : translate("email_is_available"));
    }
}
