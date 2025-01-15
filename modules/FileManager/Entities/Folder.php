<?php

namespace Modules\FileManager\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Folder extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    public $singleTitle = "folder";
    public $pluralTitle = "folders";


    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'status',
    ];
}
