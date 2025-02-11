<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Helpers\TableHelper;
use App\Helpers\IconHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Modules\Auth\Services\RegistrationService;
use Modules\Auth\Http\Requests\LoginUserRequest;
use Modules\Auth\Jobs\AttemptMailJob;
use Modules\Auth\Entities\LoginAttempt;
use Modules\Auth\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Session;
use Yajra\DataTables\Facades\DataTables;

class AuthController extends ApiController
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|Application|View
     * @throws Exception
     */
    public function showLoginForm(Request $request)
    {
        $subdomain = TenantHelper::getSubDomain();
        if ($subdomain) {
            $tenant = Tenant::where("name", $subdomain)->exists();
            if ($tenant) {
                return view('guest.auth.login', ['tenant' => $subdomain]);
            } else {
                throw new Exception("Invalid organization.");
            }
        } else {
            return view('guest.auth.login');
        }
    }

    /**
     * The function attempts to log in a user by checking their login credentials and returns a JSON
     * response indicating whether the login was successful or not.
     *
     * @param LoginUserRequest $request The  parameter is an instance of the LoginUserRequest
     * class. It contains the data submitted by the user during the login process, such as the email
     * and password.
     *
     * @return JsonResponse a JsonResponse.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $tenant = TenantHelper::makeCurrent($request->subdomain);

        if ($this->attemptLogin($request)) {
            return $this->handleSuccessfulLogin($request);
        }

        return $this->handleFailedLogin($request, $tenant);
    }

    private function attemptLogin(LoginUserRequest $request, $guard = "web"): bool
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return Auth::guard($guard)->attempt([
            $field => $request->username,
            'password' => $request->password
        ], $request->remember_me);
    }

    private function handleSuccessfulLogin(LoginUserRequest $request): JsonResponse
    {
        $user = auth()->user();
        $response = $this->collectUserDetails($user);

        $redirect = $this->handleRedirection($request);

        $language = auth()->user()->language;
        Session::put('language', $language);
        Carbon::setLocale($language->locale);

        return $this->return(200, 'User Logged in Successfully', [
            'user' => $response,
            'redirect' => $redirect
        ]);
    }

    private function handleRedirection(Request $request)
    {
        $subDomain = TenantHelper::getSubDomain();
        if ($subDomain) {
            $tenant = Tenant::where('domain', $subDomain)->first();
            if ($tenant) {
                return TenantHelper::generateURL($tenant->name) . ($request->redirect ? "?redirect=" . $request->redirect : "");
            }
        }
        return '/';
    }

    private function handleFailedLogin(LoginUserRequest $request, $tenant): JsonResponse
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $request->username)->first();

        if ($user) {
            return $this->handleExistingUserFailedLogin($user, $request, $tenant);
        }

        return $this->handleNonExistentUserLogin($request);
    }

    private function handleExistingUserFailedLogin(User $user, LoginUserRequest $request, $tenant): JsonResponse
    {
        $loginAttempt = $this->createLoginAttempt($user, $request);
        AttemptMailJob::dispatchAfterResponse($user, $loginAttempt, $tenant->id);

        return $this->return(400, 'Invalid credentials');
    }

    private function createLoginAttempt(User $user, LoginUserRequest $request): LoginAttempt
    {
        return LoginAttempt::create([
            'user_id' => $user->id,
            'agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);
    }

    private function handleNonExistentUserLogin(LoginUserRequest $request): JsonResponse
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $trashedUser = User::where($field, $request->username)->withTrashed()->first();

        if ($trashedUser) {
            return $this->attemptAccountRecovery($request, $trashedUser);
        }

        return $this->return(400, 'Invalid credentials Non Exists');
    }

    private function attemptAccountRecovery(LoginUserRequest $request, User $trashedUser): JsonResponse
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        User::where($field, $request->username)->withTrashed()->restore();

        if ($this->attemptLogin($request)) {
            return $this->handleSuccessfulRecovery($request);
        }

        $this->rollbackRecovery($trashedUser);
        return $this->return(400, 'Invalid credentials');
    }

    private function handleSuccessfulRecovery(LoginUserRequest $request): JsonResponse
    {
        $user = auth()->user();
        $response = $this->collectUserDetails($user);

        return $this->return(200, 'Account recovered successfully', [
            'user' => $response,
            'redirect' => TenantHelper::generateURL($request->subdomain)
        ]);
    }

    private function rollbackRecovery(User $trashedUser): void
    {
        User::where("email", $trashedUser->email)->update(['deleted_at' => $trashedUser->deleted_at]);
    }

    /**
     * The function collects user details and adds an access token to the user object.
     *
     * @param User $user The parameter is an instance of the `User` class.
     *
     * @return User the updated User object with the added access_token property.
     */
    public function collectUserDetails(User $user, bool $generateToken = true): User
    {
        if ($generateToken) {
            $accessToken = $this->generateAccessToken($user);
        }

        $userData = $this->selectUserData($user);
        if ($generateToken) {
            $userData['access_token'] = $accessToken;
        }

        return $userData;
    }

    private function generateAccessToken(User $user): string
    {
        return $user->createToken('web-app')->accessToken;
    }

    private function selectUserData(User $user): User
    {
        return $user->where("id", $user->id)->select('name', 'email', 'username', 'created_at')->first();
    }
    /**
     * The function logs out a user by deleting their access tokens either for a specific request or
     * for all tokens associated with the user.
     *
     * @param Request $request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains information about the request such as the request
     * method, headers, and input data. In this code, it is used to determine the type of logout action
     * to perform.
     *
     */
    public function logout(Request $request)
    {
        $user = auth()->guard('web')->user();
        try {
            if ($request->type == 1) {
                // Log out only the current session
                auth()->guard('web')->logout();
            } else {
                // Log out all sessions
                $user->tokens->each(function ($token, $key) use ($user) {
                    $token->delete();
                });
                auth()->guard('web')->logout();  // Log out the current session
            }
            return redirect()->route("login");
        } catch (Exception $e) {
            return $this->return(400, "Couldn't logout", [], ['e' => $e->getMessage()]);
        }
    }

    public function showAttempts(int $id = null)
    {
        if (request()->ajax() && request()->get('table')) {
            return $this->attemptsDatatables($id);
        }

        $route = $id ? route('landlord.attempts.index', $id) : route('attempts.index');
        return view('user.auth.login-attempts.index', compact('route'));
    }

    public function attemptsDatatables($id = null)
    {
        $rows = LoginAttempt::query()
            ->when($id, function ($query) use ($id) {
                return $query->where('user_id', $id);
            })->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, app(LoginAttempt::class)->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('agent', function ($row) {
                // TODO always add the agent as a title to the icons
                return IconHelper::formatAgentIcons($row->agent);
            })
            ->rawColumns(['agent'])
            ->make(true);
    }

    /**
     * The function "attempts" retrieves login attempts made by the authenticated user and returns them as
     * a JSON response.
     *
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function attempts(int $id = null): JsonResponse
    {
        $attempts = LoginAttempt::select(['id', 'ip', 'agent', 'created_at'])->where('user_id', auth()->guard('api')->id())->orderBy('id', 'DESC')->paginate(25);
        return $this->return(200, 'Attempts fetched successfully', ['attempts' => $attempts]);
    }

    /**
     * The `deactivate` function revokes all tokens for the authenticated user and soft deletes the
     * user account in PHP.
     *
     * @return JsonResponse A JsonResponse with a status code of 200 and a message "Account deactivated successfully" is being returned.
     */
    public function deactivate(): JsonResponse
    {
        $user = auth()->guard('api')->user();
        // Revoke all tokens
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        // set user as soft deleted
        $user->delete();
        return $this->return(200, "Account deactivated successfully");
    }
}
