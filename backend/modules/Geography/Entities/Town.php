<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Town extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "town";
    public $pluralTitle = "towns";

    protected $fillable = ['name', 'city_id', 'postalcode', 'latitude', 'longitude', 'area_km2', 'population', 'elevation_m'];

    protected $hidden = [];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
