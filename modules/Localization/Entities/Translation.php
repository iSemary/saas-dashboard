<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Translation extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "translation";
    public $pluralTitle = "translations";

    protected $fillable = [
        'language_id',
        'translation_key',
        'translation_value',
        'translation_context',
        'is_shareable',
    ];

}
