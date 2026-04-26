<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Customer\Entities\Tenant\Brand;

class ExpenseClaim extends Model
{
    use SoftDeletes;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    protected $table = 'hr_expense_claims';

    protected $fillable = [
        'employee_id',
        'category_id',
        'amount',
        'currency',
        'expense_date',
        'description',
        'receipt_path',
        'status',
        'approved_by',
        'paid_at',
        'brand_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'paid_at' => 'datetime',
    ];
}
