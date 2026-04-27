<?php

namespace Modules\Auth\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByCredentials(string $field, string $value): ?User
    {
        return User::where($field, $value)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByResetToken(string $token): ?User
    {
        return User::join('password_reset_tokens', 'password_reset_tokens.user_id', 'users.id')
            ->select(['users.id'])
            ->where('password_reset_tokens.token', $token)
            ->first();
    }

    public function updatePassword(int $userId, string $password): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        return $user->updatePassword($password);
    }

    public function deleteResetToken(string $token): void
    {
        DB::table('password_reset_tokens')->where('token', $token)->delete();
    }

    public function createResetToken(int $userId): string
    {
        $user = User::find($userId);
        return $user ? $user->createResetToken() : '';
    }

    public function formatUserData(User $user): array
    {
        $user->load(['roles.permissions', 'permissions']);

        $permissions = [];
        // Get permissions from roles
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->name;
            }
        }
        // Get direct user permissions
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission->name;
        }
        $permissions = array_values(array_unique($permissions));

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'two_factor_enabled' => !empty($user->google2fa_secret),
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            })->toArray(),
            'permissions' => $permissions,
        ];
    }
}
