<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Country extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = 'landlord';

    public $singleTitle = "country";
    public $pluralTitle = "countries";

    protected $fillable = ['name', 'code', 'region', 'flag', 'phone_code', 'timezone', 'latitude', 'longitude', 'currency_code', 'currency_symbol', 'language_code', 'area_km2', 'population'];

    protected $hidden = [];

    protected $fileColumns = [
        'flag' => [
            'folder' => 'countries',
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
    public function getFlagAttribute($value)
    {
        return $this->getFileUrl($value);
    }

    /**
     * Set the icon attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setFlagAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $media = $this->upload($value, 'flag');
            $this->attributes['flag'] = $media->id;
        } else {
            $this->attributes['flag'] = $value;
        }
    }

}
