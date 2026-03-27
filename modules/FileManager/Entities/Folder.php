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

    /**
     * Get the files associated with the folder.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get the parent folder.
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Get the child folders.
     */
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }
}
