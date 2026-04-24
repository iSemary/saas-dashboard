<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\LeadFactory;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'title',
        'description',
        'status',
        'source',
        'expected_revenue',
        'expected_close_date',
        'assigned_to',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'expected_close_date' => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Get the user assigned to this lead.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this lead.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the activities for this lead.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope for filtering by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Convert lead to opportunity.
     */
    public function convertToOpportunity($opportunityData = [])
    {
        $opportunity = Opportunity::create(array_merge([
            'name' => $this->name,
            'lead_id' => $this->id,
            'expected_revenue' => $this->expected_revenue,
            'expected_close_date' => $this->expected_close_date,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
        ], $opportunityData));

        $this->update(['status' => 'converted']);

        return $opportunity;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return LeadFactory::new();
    }
}
