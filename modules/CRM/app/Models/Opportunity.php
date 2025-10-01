<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'lead_id',
        'contact_id',
        'company_id',
        'description',
        'stage',
        'expected_revenue',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'assigned_to',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'probability' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Get the lead associated with this opportunity.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the contact associated with this opportunity.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the company associated with this opportunity.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user assigned to this opportunity.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this opportunity.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the activities for this opportunity.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'related_id')->where('related_type', self::class);
    }

    /**
     * Scope for filtering by stage.
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope for filtering by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Calculate weighted revenue based on probability.
     */
    public function getWeightedRevenueAttribute()
    {
        return $this->expected_revenue * ($this->probability / 100);
    }

    /**
     * Close opportunity as won.
     */
    public function closeWon($actualRevenue = null)
    {
        $this->update([
            'stage' => 'closed_won',
            'actual_close_date' => now(),
            'expected_revenue' => $actualRevenue ?? $this->expected_revenue,
            'probability' => 100,
        ]);
    }

    /**
     * Close opportunity as lost.
     */
    public function closeLost()
    {
        $this->update([
            'stage' => 'closed_lost',
            'actual_close_date' => now(),
            'probability' => 0,
        ]);
    }
}
