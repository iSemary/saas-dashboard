<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    public $singleTitle = "city";
    public $pluralTitle = "cities";
    
    protected $fillable = [
        'name',
        'code',
        'capital_city_id',
        'region',
        'flag',
        'phone_code'
    ];

    protected $hidden = [];
}
