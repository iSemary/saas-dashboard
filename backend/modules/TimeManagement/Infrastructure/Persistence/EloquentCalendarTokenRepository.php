<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\CalendarToken;
use Illuminate\Database\Eloquent\Collection;

class EloquentCalendarTokenRepository implements CalendarTokenRepositoryInterface
{
    public function find(string $id): ?CalendarToken
    {
        return CalendarToken::find($id);
    }

    public function findOrFail(string $id): CalendarToken
    {
        return CalendarToken::findOrFail($id);
    }

    public function updateOrCreate(array $attributes, array $values): CalendarToken
    {
        return CalendarToken::updateOrCreate($attributes, $values);
    }

    public function deleteByUserAndProvider(string $userId, string $provider): bool
    {
        return CalendarToken::where('user_id', $userId)
            ->where('provider', $provider)
            ->delete() > 0;
    }

    public function getByUser(string $userId): Collection
    {
        return CalendarToken::where('user_id', $userId)->get();
    }
}
