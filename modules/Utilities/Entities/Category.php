<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "category";
    public $pluralTitle = "categories";

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'icon', 'priority', 'status'];
}
