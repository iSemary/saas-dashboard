<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = "landlord";

    public $singleTitle = "tag";
    public $pluralTitle = "tags";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'icon',
        'priority'
    ];
}
