<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\ContactFactory;
use Modules\CRM\Domain\ValueObjects\CompanyType;
use Modules\CRM\Domain\ValueObjects\Address;
use Modules\CRM\Domain\Exceptions\DuplicateContactException;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'company_id',
        'type',
        'assigned_to',
        'created_by',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($contact) {
            if (empty($contact->type)) {
                $contact->type = CompanyType::CUSTOMER->value;
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
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

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ── Business Methods ─────────────────────────────────

    /**
     * Get full name.
     */
    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check for duplicate by email.
     *
     * @throws DuplicateContactException
     */
    public static function checkDuplicateEmail(string $email, ?int $excludeId = null): ?self
    {
        $query = self::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Assign to user.
     */
    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    /**
     * Link to company.
     */
    public function linkToCompany(int $companyId): void
    {
        $this->update(['company_id' => $companyId]);
    }

    /**
     * Get address as Value Object.
     */
    public function getAddressValueObject(): Address
    {
        return new Address(
            street: null, // Not stored separately in contacts table
            city: $this->city,
            state: $this->state,
            postalCode: $this->postal_code,
            country: $this->country,
        );
    }

    /**
     * Check if contact has opportunities.
     */
    public function hasOpportunities(): bool
    {
        return $this->opportunities()->exists();
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
        return ContactFactory::new();
    }
}
