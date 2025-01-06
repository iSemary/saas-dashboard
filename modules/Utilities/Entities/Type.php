<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory, SoftDeletes;

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
