<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory, SoftDeletes;

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
