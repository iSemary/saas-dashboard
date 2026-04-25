<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Module extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = "landlord";

    public $singleTitle = "module";
    public $pluralTitle = "modules";

    protected $fillable = [
        'module_key',
        'name',
        'description',
        'route',
        'icon',
        'slogan',
        'navigation',
        'theme',
        'status',
    ];

    protected $casts = [
        'navigation' => 'array',
        'theme' => 'array',
    ];

    protected $fileColumns = [
        'icon' => [
            'folder' => 'modules',
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
        return $this->getFileUrl($value ? (int) $value : null);
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

    public function moduleEntities()
    {
        return $this->hasMany(ModuleEntity::class);
    }
}
