<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_candidates';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'location',
        'current_title',
        'current_company',
        'linkedin_url',
        'portfolio_url',
        'resume_path',
        'source', // direct, referral, agency, job_board
        'referrer_id',
        'tags',
        'rating', // 1-5 stars
        'notes',
        'blacklisted',
        'blacklist_reason',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'rating' => 'decimal:1',
        'blacklisted' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeAvailable($query)
    {
        return $query->where('blacklisted', false);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('blacklisted', true);
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }
}
