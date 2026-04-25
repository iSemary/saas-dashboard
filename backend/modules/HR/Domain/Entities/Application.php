<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_opening_id',
        'candidate_id',
        'pipeline_stage_id',
        'status', // new, screening, interview, offer, hired, rejected
        'applied_at',
        'cover_letter',
        'answers', // Custom application questions
        'source',
        'rejection_reason',
        'rejected_at',
        'rejected_by',
        'salary_expectation',
        'currency',
        'available_from',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'answers' => 'array',
        'applied_at' => 'datetime',
        'rejected_at' => 'datetime',
        'salary_expectation' => 'decimal:2',
        'available_from' => 'date',
    ];

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['hired', 'rejected']);
    }

    public function scopeByStage($query, int $stageId)
    {
        return $query->where('pipeline_stage_id', $stageId);
    }

    public function advanceToStage(PipelineStage $stage): void
    {
        $this->update([
            'pipeline_stage_id' => $stage->id,
            'status' => $stage->maps_to_status,
        ]);
    }

    public function reject(string $reason, int $rejectedBy): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'rejected_by' => $rejectedBy,
        ]);
    }

    public function markAsHired(): void
    {
        $this->update(['status' => 'hired']);
        $this->jobOpening->incrementFilled();
    }
}
