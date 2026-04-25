<?php

namespace Modules\POS\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class Damaged extends Model
{
    use SoftDeletes;

    protected $table = 'pos_damaged';

    protected $fillable = ['product_id', 'branch_id', 'amount', 'created_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
