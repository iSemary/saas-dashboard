<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Industry extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "industry";
    public $pluralTitle = "industries";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'icon',
        'priority'
    ];
}
