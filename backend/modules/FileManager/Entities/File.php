<?php

namespace Modules\FileManager\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class File extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    public $singleTitle = "file";
    public $pluralTitle = "files";


    protected $fillable = [
        'folder_id',
        'hash_name',
        'checksum',
        'original_name',
        'mime_type',
        'host',
        'status',
        'access_level',
        'size',
        'metadata',
        'is_encrypted'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the folder that owns the file.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
