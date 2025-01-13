<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Type extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "type";
    public $pluralTitle = "types";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'icon',
        'priority'
    ];
}
