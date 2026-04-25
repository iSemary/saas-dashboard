<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseClaim extends Model
{
    use SoftDeletes;

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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'paid_at' => 'datetime',
    ];
}
