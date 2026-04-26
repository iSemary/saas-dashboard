<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\CalendarToken;
use Illuminate\Database\Eloquent\Collection;

interface CalendarTokenRepositoryInterface
{
    public function find(string $id): ?CalendarToken;
    public function findOrFail(string $id): CalendarToken;
    public function updateOrCreate(array $attributes, array $values): CalendarToken;
    public function deleteByUserAndProvider(string $userId, string $provider): bool;
    public function getByUser(string $userId): Collection;
}
