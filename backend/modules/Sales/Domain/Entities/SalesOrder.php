<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Auth\Entities\User;
use Modules\Sales\Domain\Enums\OrderStatus;
use Modules\Sales\Domain\Enums\PaymentMethod;
use Modules\Sales\Domain\Enums\SalesOrderType;

class SalesOrder extends Model
{
    use SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'user_id', 'branch_id', 'products', 'total_price', 'amount_paid',
        'tax', 'barcode', 'pay_method', 'transaction_number', 'status', 'order_type',
    ];

    protected $casts = [
        'products'    => 'array',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'tax'         => 'decimal:2',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function installment(): HasOne
    {
        return $this->hasOne(SalesOrderInstallment::class, 'order_id');
    }

    public function orderTouch(): HasOne
    {
        return $this->hasOne(SalesOrderTouch::class, 'order_id');
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(SalesDelivery::class, 'order_id');
    }

    public function steward(): HasOne
    {
        return $this->hasOne(SalesOrderSteward::class, 'order_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(SalesClient::class, 'sales_client_orders', 'order_id', 'client_id');
    }

    // ─── Domain methods ───────────────────────────────────────────

    public function getStatusEnum(): OrderStatus
    {
        return OrderStatus::from($this->status);
    }

    public function getPayMethodEnum(): PaymentMethod
    {
        return PaymentMethod::from($this->pay_method);
    }

    public function getOrderTypeEnum(): SalesOrderType
    {
        return SalesOrderType::from($this->order_type);
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed->value;
    }

    public function isInstallment(): bool
    {
        return $this->pay_method === PaymentMethod::Installment->value;
    }

    public function getTotalProductCount(): int
    {
        return collect($this->products)->sum('quantity');
    }

    public function getChangeAmount(): float
    {
        return max(0, (float) $this->amount_paid - (float) $this->total_price);
    }
}
