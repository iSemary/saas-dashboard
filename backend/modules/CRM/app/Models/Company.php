<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'industry',
        'employee_count',
        'annual_revenue',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'description',
        'notes',
        'type',
        'assigned_to',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    /**
     * Get the user assigned to this company.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this company.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the contacts for this company.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the opportunities for this company.
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * Get the activities for this company.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'related_id')->where('related_type', self::class);
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by industry.
     */
    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Scope for filtering by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Get the primary contact for this company.
     */
    public function primaryContact()
    {
        return $this->contacts()->where('type', 'individual')->first();
    }
}
