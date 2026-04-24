<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "unit";
    public $pluralTitle = "units";

    protected $fillable = [
        'name',
        'code',
        'type_id',
        'base_conversion',
        'description',
        'is_base_unit'
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'base_conversion' => 'decimal:5'
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
