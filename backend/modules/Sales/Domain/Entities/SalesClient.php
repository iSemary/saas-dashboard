<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Auth\Entities\User;

class SalesClient extends Model
{
    use SoftDeletes;

    protected $table = 'sales_clients';

    protected $fillable = ['user_id', 'code', 'phone', 'address', 'gift', 'created_by'];

    protected $casts = ['phone' => 'array', 'gift' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(SalesOrder::class, 'sales_client_orders', 'client_id', 'order_id');
    }

    public function applyGift(float $amount): void
    {
        if ($this->gift < $amount) {
            throw new \DomainException("Insufficient gift balance. Available: {$this->gift}");
        }
        $this->decrement('gift', $amount);
    }
}
