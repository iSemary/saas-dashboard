<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\CompanyFactory;
use Modules\CRM\Domain\ValueObjects\CompanyType;
use Modules\CRM\Domain\ValueObjects\Address;
use Modules\CRM\Domain\ValueObjects\Money;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

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
        'parent_id',
        'custom_fields',
    ];

    protected $casts = [
        'employee_count' => 'integer',
        'annual_revenue' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->type)) {
                $company->type = CompanyType::CUSTOMER->value;
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CrmNote::class, 'related');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CrmFile::class, 'related');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->has('children');
    }

    // ── Business Methods ─────────────────────────────────

    /**
     * Assign to user.
     */
    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    /**
     * Set parent company.
     */
    public function setParent(?int $parentId): void
    {
        // Prevent circular reference
        if ($parentId === $this->id) {
            throw new \InvalidArgumentException('Company cannot be its own parent');
        }

        $this->update(['parent_id' => $parentId]);
    }

    /**
     * Check if company has parent.
     */
    public function hasParent(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Check if company has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get address as Value Object.
     */
    public function getAddressValueObject(): Address
    {
        return new Address(
            street: $this->address,
            city: $this->city,
            state: $this->state,
            postalCode: $this->postal_code,
            country: $this->country,
        );
    }

    /**
     * Get annual revenue as Money Value Object.
     */
    public function getAnnualRevenueMoney(): ?Money
    {
        if ($this->annual_revenue === null) {
            return null;
        }

        return Money::fromFloat($this->annual_revenue);
    }

    /**
     * Get total revenue from all opportunities.
     */
    public function getTotalOpportunityRevenue(): float
    {
        return $this->opportunities()->sum('expected_revenue') ?? 0;
    }

    /**
     * Get won revenue from closed opportunities.
     */
    public function getWonRevenue(): float
    {
        return $this->opportunities()
            ->where('stage', 'closed_won')
            ->sum('expected_revenue') ?? 0;
    }

    // ── Accessors ─────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return CompanyType::fromString($this->type)->label();
    }

    public function getTypeColorAttribute(): string
    {
        return CompanyType::fromString($this->type)->color();
    }

    protected static function newFactory()
    {
        return CompanyFactory::new();
    }
}
