<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Release extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "release";
    public $pluralTitle = "releases";

    protected $fillable = [
        'object_model',
        'object_id',
        'name',
        'slug',
        'description',
        'body',
        'version',
        'status',
        'release_date',
    ];
}
