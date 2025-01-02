<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    public $singleTitle = "province";
    public $pluralTitle = "provinces";
    
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
