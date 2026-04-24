<?php

namespace Modules\Development\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Utilities\Entities\Type;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Crypt;

class Configuration extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "configuration";
    public $pluralTitle = "configurations";

    protected $fillable = [
        'configuration_key',
        'configuration_value',
        'description',
        'type_id',
        'input_type',
        'is_encrypted',
        'is_system',
        'is_visible',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_system' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the configuration value with automatic decryption if needed.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        if ($this->is_encrypted && $this->configuration_value) {
            return Crypt::decryptString($this->configuration_value);
        }

        return $this->configuration_value;
    }

    /**
     * Set the configuration value with automatic encryption if needed.
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted) {
            $this->attributes['configuration_value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['configuration_value'] = $value;
        }
    }

    /**
     * Scope a query to only include visible configurations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include system configurations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
