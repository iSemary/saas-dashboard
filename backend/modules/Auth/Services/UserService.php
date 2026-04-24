<?php

namespace Modules\Auth\Services;

use Modules\Auth\Entities\User;
use Modules\Auth\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users
     */
    public function getAll(array $conditions = []): Collection
    {
        $query = User::query();

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get user by ID
     */
    public function get(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user by username
     */
    public function getByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        return $this->userRepository->create($data);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->get($id);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    /**
     * Delete user (soft delete)
     */
    public function delete(int $id): bool
    {
        $user = $this->get($id);
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Restore user
     */
    public function restore(int $id): bool
    {
        $user = User::withTrashed()->find($id);
        if (!$user) {
            return false;
        }

        return $user->restore();
    }

    /**
     * Get users count
     */
    public function count(): int
    {
        return User::count();
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::whereNotIn('status', ['inactive'])->orWhereNull('status')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];
    }

    /**
     * Get total count
     */
    public function getTotalCount(): int
    {
        return User::count();
    }

    /**
     * Get active count
     */
    public function getActiveCount(): int
    {
        return User::whereNotIn('status', ['inactive'])->orWhereNull('status')->count();
    }

    /**
     * Get users with specific role
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::role($role)->get();
    }
}