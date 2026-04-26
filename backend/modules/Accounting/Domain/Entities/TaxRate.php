<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class TaxRate extends Model
{
    use SoftDeletes;

    protected $table = 'acc_tax_rates';

    protected $fillable = [
        'name',
        'rate',
        'type',
        'account_id',
        'is_active',
        'description',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function calculate(float $amount): float
    {
        return $amount * ($this->rate / 100);
    }

    // ── Relationships ─────────────────────────────────────

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
