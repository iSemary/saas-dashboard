<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'candidate_id',
        'type', // phone, video, in_person, panel
        'scheduled_at',
        'duration_minutes',
        'location',
        'meeting_link',
        'status', // scheduled, completed, cancelled, no_show
        'feedback',
        'rating',
        'recommendation', // hire, reject, second_interview
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'feedback' => 'array',
        'rating' => 'decimal:1',
        'completed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'interview_interviewer', 'interview_id', 'interviewer_id')
            ->withPivot('feedback', 'rating', 'completed')
            ->withTimestamps();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function complete(array $feedback = [], ?float $rating = null, ?string $recommendation = null): void
    {
        $updateData = [
            'status' => 'completed',
            'completed_at' => now(),
        ];

        if ($feedback) {
            $updateData['feedback'] = $feedback;
        }
        if ($rating !== null) {
            $updateData['rating'] = $rating;
        }
        if ($recommendation) {
            $updateData['recommendation'] = $recommendation;
        }

        $this->update($updateData);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
