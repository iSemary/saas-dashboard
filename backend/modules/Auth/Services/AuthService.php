<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Entities\User;
use Modules\Auth\Repositories\AuthRepositoryInterface;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $repository) {}

    public function formatUserData(User $user): array
    {
        return $this->repository->formatUserData($user);
    }

    public function login(string $email, string $password, ?string $subdomain = null): array
    {
        if (!$subdomain || $subdomain === 'www' || $subdomain === '') {
            return ['error' => 'Invalid organization. Provide a subdomain in the request body.', 'code' => 400];
        }

        $landlordOrg = env('APP_LANDLORD_ORGANIZATION_NAME', 'landlord');
        if ($subdomain === $landlordOrg) {
            TenantHelper::makeCurrent($landlordOrg);
        } else {
            $tenant = Tenant::where('domain', $subdomain)->first();
            if (!$tenant) {
                return ['error' => 'Tenant not found', 'code' => 400];
            }
            TenantHelper::makeCurrent($tenant->name);
        }

        $field = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $email, 'password' => $password];

        if (!Auth::guard('web')->attempt($credentials)) {
            return ['error' => 'Invalid credentials', 'code' => 401];
        }

        $user = Auth::guard('web')->user();

        if ($user->google2fa_secret) {
            $tempToken = $this->generateTempToken($user, $subdomain);
            Auth::guard('web')->logout();

            return [
                'requires_2fa' => true,
                'temp_token' => $tempToken,
            ];
        }

        $token = $user->createToken('web-app')->accessToken;
        $userData = $this->repository->formatUserData($user);

        return [
            'token' => $token,
            'user' => $userData,
        ];
    }

    public function verify2FA(string $tempToken, string $code, ?string $subdomain = null): array
    {
        $cacheKey = "2fa_temp_token_{$tempToken}";
        $cached = cache()->get($cacheKey);
        if (!$cached) {
            return ['error' => 'Invalid temporary token', 'code' => 401];
        }

        $userId = is_array($cached) ? ($cached['user_id'] ?? null) : $cached;
        $cachedSubdomain = is_array($cached) ? ($cached['subdomain'] ?? null) : null;

        $subdomain = $subdomain ?: $cachedSubdomain ?: TenantHelper::getSubDomain();

        if ($subdomain) {
            $landlordOrg = env('APP_LANDLORD_ORGANIZATION_NAME', 'landlord');
            if ($subdomain === $landlordOrg) {
                TenantHelper::makeCurrent($landlordOrg);
            } else {
                $tenant = Tenant::where('domain', $subdomain)->first();
                if ($tenant) {
                    TenantHelper::makeCurrent($tenant->name);
                }
            }
        }

        $user = $this->repository->findById($userId);
        if (!$user) {
            return ['error' => 'Invalid temporary token', 'code' => 401];
        }

        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $code);

        if (!$isValid) {
            return ['error' => 'Invalid verification code', 'code' => 422];
        }

        Auth::guard('web')->login($user);
        $token = $user->createToken('web-app')->accessToken;
        $userData = $this->repository->formatUserData($user);

        return [
            'token' => $token,
            'user' => $userData,
        ];
    }

    public function setup2FA(User $user): array
    {
        if ($user->google2fa_secret) {
            return ['error' => '2FA is already enabled', 'code' => 400];
        }

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeInline(config('app.name'), $user->email, $secret);

        return [
            'secret' => $secret,
            'qr_code' => $qrCodeUrl,
        ];
    }

    public function confirm2FA(User $user, string $code, string $secret): array
    {
        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($secret, $code);

        if (!$isValid) {
            return ['error' => 'Invalid verification code', 'code' => 422];
        }

        $user->update(['google2fa_secret' => $secret]);
        $recoveryCodes = $this->generateRecoveryCodes();

        return ['recovery_codes' => $recoveryCodes];
    }

    public function disable2FA(User $user): void
    {
        $user->update(['google2fa_secret' => null]);
    }

    public function forgotPassword(string $email, ?string $subdomain = null): array
    {
        if ($subdomain) {
            $landlordOrg = env('APP_LANDLORD_ORGANIZATION_NAME', 'landlord');
            if ($subdomain === $landlordOrg) {
                TenantHelper::makeCurrent($landlordOrg);
            } else {
                $tenant = Tenant::where('domain', $subdomain)->first();
                if ($tenant) {
                    TenantHelper::makeCurrent($tenant->name);
                }
            }
        }

        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return ['message' => translate('message.action_completed')];
        }

        $token = $this->repository->createResetToken($user->id);
        $tenantId = $subdomain ? Tenant::where('domain', $subdomain)->first()?->id : null;
        \Modules\Auth\Jobs\ForgetPasswordMailJob::dispatch($user, $token, $tenantId);

        return ['message' => translate('message.action_completed')];
    }

    public function resetPassword(string $token, string $password, ?string $subdomain = null): array
    {
        if ($subdomain) {
            $landlordOrg = env('APP_LANDLORD_ORGANIZATION_NAME', 'landlord');
            if ($subdomain === $landlordOrg) {
                TenantHelper::makeCurrent($landlordOrg);
            } else {
                $tenant = Tenant::where('domain', $subdomain)->first();
                if ($tenant) {
                    TenantHelper::makeCurrent($tenant->name);
                }
            }
        }

        $user = $this->repository->findByResetToken($token);
        if (!$user) {
            return ['error' => 'Invalid or expired token.', 'code' => 400];
        }

        $this->repository->updatePassword($user->id, $password);
        $this->repository->deleteResetToken($token);

        return ['message' => translate('message.action_completed')];
    }

    public function updateProfile(User $user, array $validated): User
    {
        $user->update($validated);

        if (isset($validated['phone']) || isset($validated['address']) || isset($validated['timezone'])) {
            $user->setMeta([
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'timezone' => $validated['timezone'] ?? null,
            ]);
        }

        return $user->fresh();
    }

    public function uploadAvatar(User $user, $avatar): array
    {
        $path = $avatar->store('avatars', 'public');
        $user->setMeta(['avatar' => $path]);

        return ['avatar' => Storage::url($path)];
    }

    public function removeAvatar(User $user): void
    {
        $avatar = $user->meta('avatar');
        if ($avatar && Storage::disk('public')->exists($avatar)) {
            Storage::disk('public')->delete($avatar);
        }
        $user->setMeta(['avatar' => null]);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return ['error' => 'Current password is incorrect', 'code' => 422];
        }

        $user->update(['password' => Hash::make($newPassword)]);

        return ['message' => translate('message.action_completed')];
    }

    public function getSessions(User $user)
    {
        $tokens = $user->tokens()->get();

        return $tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'created_at' => $token->created_at->toIso8601String(),
            ];
        });
    }

    public function revokeSession(User $user, int $sessionId): array
    {
        $token = $user->tokens()->find($sessionId);
        if (!$token) {
            return ['error' => 'Session not found', 'code' => 404];
        }
        $token->revoke();
        return ['message' => translate('message.action_completed')];
    }

    public function getApiKeys(User $user)
    {
        $tokens = $user->tokens()->get();

        return $tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'created_at' => $token->created_at->toIso8601String(),
                'scopes' => $token->scopes ?? [],
            ];
        });
    }

    public function createApiKey(User $user, string $name, array $scopes = ['*']): array
    {
        $token = $user->createToken($name, $scopes);

        return [
            'id' => $token->token->id,
            'name' => $token->token->name,
            'token' => $token->accessToken,
            'created_at' => $token->token->created_at->toIso8601String(),
        ];
    }

    public function revokeApiKey(User $user, int $keyId): array
    {
        $token = $user->tokens()->find($keyId);
        if (!$token) {
            return ['error' => 'API key not found', 'code' => 404];
        }
        $token->revoke();
        return ['message' => translate('message.action_completed')];
    }

    public function globalSearch(string $query, array $types = []): array
    {
        $results = [];

        if (empty($types) || in_array('customers', $types)) {
            try {
                $companies = \Modules\CRM\Models\Company::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();

                $results['customers'] = $companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'type' => 'customer',
                        'title' => $company->name,
                        'description' => $company->email,
                        'url' => "/dashboard/customers/{$company->id}",
                    ];
                });
            } catch (\Exception $e) {
                $results['customers'] = [];
            }
        }

        if (empty($types) || in_array('tickets', $types)) {
            try {
                $tickets = \Modules\Ticket\Entities\Ticket::where('title', 'like', "%{$query}%")
                    ->orWhere('ticket_number', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();

                $results['tickets'] = $tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'type' => 'ticket',
                        'title' => $ticket->title,
                        'description' => $ticket->ticket_number,
                        'url' => "/dashboard/tickets/{$ticket->id}",
                    ];
                });
            } catch (\Exception $e) {
                $results['tickets'] = [];
            }
        }

        if (empty($types) || in_array('documents', $types)) {
            try {
                $files = \Modules\FileManager\Entities\File::where('original_name', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();

                $results['documents'] = $files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'type' => 'document',
                        'title' => $file->original_name,
                        'description' => $file->mime_type,
                        'url' => "/dashboard/documents",
                    ];
                });
            } catch (\Exception $e) {
                $results['documents'] = [];
            }
        }

        return $results;
    }

    protected function generateTempToken(User $user, ?string $subdomain = null): string
    {
        $tempToken = bin2hex(random_bytes(32));
        $cacheValue = $subdomain
            ? ['user_id' => $user->id, 'subdomain' => $subdomain]
            : $user->id;
        cache()->put("2fa_temp_token_{$tempToken}", $cacheValue, now()->addMinutes(10));
        return $tempToken;
    }

    protected function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }
}
