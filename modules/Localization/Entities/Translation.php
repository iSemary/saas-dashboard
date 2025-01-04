<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    public $singleTitle = "translation";
    public $pluralTitle = "translations";
    
    protected $fillable = [
        'language_id',
        'translation_key',
        'translation_value',
        'translation_context',
    ];

    protected $hidden = [];
}
