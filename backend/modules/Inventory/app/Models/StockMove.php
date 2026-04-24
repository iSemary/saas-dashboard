<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Auth\Entities\User;

class StockMove extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reference',
        'product_id',
        'warehouse_id',
        'move_type',
        'origin_type',
        'origin_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'date',
        'state',
        'description',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'date' => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Get the product for this stock move.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\Modules\Sales\Models\Product::class);
    }

    /**
     * Get the warehouse for this stock move.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user who created this stock move.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the origin document (polymorphic).
     */
    public function origin(): MorphTo
    {
        return $this->morphTo('origin', 'origin_type', 'origin_id');
    }

    /**
     * Scope for confirmed stock moves.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('state', 'confirmed');
    }

    /**
     * Scope for done stock moves.
     */
    public function scopeDone($query)
    {
        return $query->where('state', 'done');
    }

    /**
     * Scope for incoming stock moves.
     */
    public function scopeIncoming($query)
    {
        return $query->where('move_type', 'in');
    }

    /**
     * Scope for outgoing stock moves.
     */
    public function scopeOutgoing($query)
    {
        return $query->where('move_type', 'out');
    }

    /**
     * Confirm the stock move.
     */
    public function confirm()
    {
        $this->update(['state' => 'confirmed']);
    }

    /**
     * Mark the stock move as done.
     */
    public function markAsDone()
    {
        $this->update(['state' => 'done']);
    }

    /**
     * Cancel the stock move.
     */
    public function cancel()
    {
        $this->update(['state' => 'cancel']);
    }

    /**
     * Check if stock move is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->state === 'confirmed';
    }

    /**
     * Check if stock move is done.
     */
    public function isDone(): bool
    {
        return $this->state === 'done';
    }

    /**
     * Check if stock move is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->state === 'cancel';
    }
}
