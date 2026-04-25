<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback';

    protected $fillable = [
        'recipient_id',
        'provider_id',
        'type',
        'category',
        'content',
        'is_anonymous',
        'is_public',
        'acknowledged_at',
        'created_by',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_public' => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'provider_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('recipient_id', $employeeId);
    }

    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }
}
