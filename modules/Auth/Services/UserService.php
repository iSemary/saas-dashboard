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
     * Get active users count
     */
    public function countActive(): int
    {
        return User::whereNull('deleted_at')->count();
    }

    /**
     * Get users with specific role
     */
    public function getByRole(string $roleName): Collection
    {
        return User::role($roleName)->get();
    }

    /**
     * Get users created in date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return User::whereBetween('created_at', [$startDate, $endDate])->get();
    }

    /**
     * Get recent users
     */
    public function getRecent(int $limit = 10): Collection
    {
        return User::latest()->limit($limit)->get();
    }

    /**
     * Search users
     */
    public function search(string $query): Collection
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->count(),
            'active' => $this->countActive(),
            'deleted' => User::onlyTrashed()->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];
    }

    /**
     * Get user growth data for charts
     */
    public function getGrowthData(int $days = 30): array
    {
        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $count = User::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ];
        }

        return $data;
    }
}
