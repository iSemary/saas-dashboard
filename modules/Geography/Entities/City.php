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
    
    protected $fillable = ['name', 'province_id'];
    
    protected $hidden = [];
}
