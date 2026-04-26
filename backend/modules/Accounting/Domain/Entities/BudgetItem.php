<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    use SoftDeletes;

    protected $table = 'acc_budget_items';

    protected $fillable = [
        'budget_id',
        'account_id',
        'amount',
        'description',
        'custom_fields',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
