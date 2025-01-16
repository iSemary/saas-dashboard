<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = "landlord";

    public $singleTitle = "category";
    public $pluralTitle = "categories";

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'icon', 'priority', 'status'];

    protected $imageColumns = [
        'icon' => [
            'folder' => 'categories',
            'is_encrypted' => false,
            'access_level' => 'public',
            'metadata' => ['width', 'height', 'aspect_ratio'],
        ],
    ];

    /**
     * Get the icon URL dynamically.
     *
     * @return string
     */
    public function getIconAttribute($value)
    {
        return $this->getFileUrl($value);
    }
}
