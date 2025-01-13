<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Language extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "language";
    public $pluralTitle = "languages";
    
    protected $fillable = [
        'name',
        'locale',
        'direction',
    ];

    protected $hidden = [];
}
