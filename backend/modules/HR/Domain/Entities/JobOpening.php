<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOpening extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_openings';

    protected $fillable = [
        'title',
        'department_id',
        'position_id',
        'location',
        'type', // full-time, part-time, contract, internship
        'employment_type', // permanent, temporary
        'salary_min',
        'salary_max',
        'currency',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'status', // draft, published, closed, on_hold
        'published_at',
        'closes_at',
        'hiring_manager_id',
        'recruiter_id',
        'vacancies',
        'filled_count',
        'created_by',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'published_at' => 'datetime',
        'closes_at' => 'datetime',
        'vacancies' => 'integer',
        'filled_count' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function hiringManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'hiring_manager_id');
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recruiter_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('closes_at')
                  ->orWhere('closes_at', '>=', now());
            });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'draft']);
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function isFilled(): bool
    {
        return $this->filled_count >= $this->vacancies;
    }

    public function incrementFilled(): void
    {
        $this->increment('filled_count');
        if ($this->isFilled()) {
            $this->close();
        }
    }
}
