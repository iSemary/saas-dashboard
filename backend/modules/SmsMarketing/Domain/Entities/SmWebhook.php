<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SmWebhook extends Model
{
    use SoftDeletes;

    protected $table = 'sm_webhooks';

    protected $fillable = [
        'name', 'url', 'events', 'secret', 'is_active', 'created_by',
    ];

    protected $hidden = ['secret'];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public static function generateSecret(): string
    {
        return Str::random(64);
    }

    public function toggle(): void
    {
        $this->is_active = ! $this->is_active;
    }

    public function sign(array $payload): string
    {
        return hash_hmac('sha256', json_encode($payload), $this->secret);
    }
}
