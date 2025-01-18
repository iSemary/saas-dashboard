<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use Modules\Localization\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler, Translatable;

    protected $connection = "landlord";

    public $singleTitle = "category";
    public $pluralTitle = "categories";

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'icon', 'priority', 'status'];

    protected $translatableColumns = ['name', 'description'];

    public function getNameAttribute()
    {
        return $this->getTranslatable('name');
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslatable('description');
    }

    protected $fileColumns = [
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

    /**
     * Set the icon attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setIconAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $media = $this->upload($value, 'icon');
            $this->attributes['icon'] = $media->id;
        } else {
            $this->attributes['icon'] = $value;
        }
    }
}
