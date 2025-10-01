<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Street extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';
    
    public $singleTitle = "street";
    public $pluralTitle = "streets";
    
    protected $fillable = ['name', 'postal_code', 'town_id', 'latitude', 'longitude', 'area_km2', 'population', 'elevation_m', 'phone_code', 'timezone'];
    
    protected $hidden = [];
}
