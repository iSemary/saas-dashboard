<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $connection = 'landlord';

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
