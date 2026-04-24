<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Currency extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "currency";
    public $pluralTitle = "currencies";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'exchange_rate',
        'exchange_rate_last_updated',
        'symbol_position',
        'base_currency',
        'priority',
        'note',
        'status',
    ];

    /**
     * Scope to filter active currencies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get currencies sorted by priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Format the exchange rate with the appropriate decimal places.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getFormattedExchangeRateAttribute($value)
    {
        return number_format($this->exchange_rate, $this->decimal_places);
    }

    /**
     * Get the currency symbol with its position (left or right).
     *
     * @return string
     */
    public function getFormattedSymbolAttribute()
    {
        if ($this->symbol_position === 'left') {
            return $this->symbol . ' ' . $this->name;
        } else {
            return $this->name . ' ' . $this->symbol;
        }
    }
}
