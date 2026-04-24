<?php

namespace Modules\Auth\Repositories;

use Modules\Auth\Entities\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuthRepositoryInterface
{
    public function findByCredentials(string $field, string $value): ?User;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByResetToken(string $token): ?User;

    public function updatePassword(int $userId, string $password): bool;

    public function deleteResetToken(string $token): void;

    public function createResetToken(int $userId): string;

    public function formatUserData(User $user): array;
}
