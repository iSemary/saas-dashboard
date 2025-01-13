<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Country extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "country";
    public $pluralTitle = "countries";

    protected $fillable = ['name', 'code', 'region', 'flag', 'phone_code'];

    protected $hidden = [];
}
