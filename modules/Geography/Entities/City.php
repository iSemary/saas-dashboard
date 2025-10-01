<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class City extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';
    
    public $singleTitle = "city";
    public $pluralTitle = "cities";
    
    protected $fillable = ['name', 'postal_code', 'is_capital', 'phone_code', 'timezone', 'province_id', 'latitude', 'longitude', 'area_km2', 'population', 'elevation_m'];
    
    protected $hidden = [];
}
