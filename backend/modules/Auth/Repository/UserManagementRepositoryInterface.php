<?php

namespace Modules\Auth\Repository;

use Modules\Auth\Entities\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserManagementRepositoryInterface
{
    public function getUsersWithFilters(array $filters = [], int $perPage = 15, array $orderBy = []): LengthAwarePaginator;
    public function createUser(array $userData): User;
    public function updateUser(int $userId, array $userData): bool;
    public function deleteUser(int $userId): bool;
    public function findUser(int $userId): User|null;
    public function findUserWithRelations(int $userId): User|null;
    public function findUserWithRoles(int $userId): User|null;
    public function findUserWithDirectPermissions(int $userId): User|null;
    public function findUserWithRolesAndPermissions(int $userId): User|null;
    public function setUserMeta(int $userId, array $metaData): bool;
    public function getUserStatistics(): array;
    public function searchUsers(string $query, int $limit = 10): array;
    public function getUsersByRole(string $_roleName): array;
    public function getActiveUsersCount(): int;
    public function getInactiveUsersCount(): int;
    public function getNewUsersCount(int $days = 30): int;
}
