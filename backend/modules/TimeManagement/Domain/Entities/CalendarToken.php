<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class CalendarToken extends Model
{
    protected $table = 'tm_calendar_tokens';

    protected $fillable = [
        'tenant_id', 'user_id', 'provider',
        'access_token', 'refresh_token', 'expires_at', 'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }
}
