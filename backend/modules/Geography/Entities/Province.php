<?php

namespace Modules\Geography\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Province extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = 'landlord';

    public $singleTitle = "province";
    public $pluralTitle = "provinces";

    protected $fillable = ['name', 'country_id', 'is_capital', 'flag', 'phone_code', 'timezone', 'latitude', 'longitude', 'area_km2', 'population', 'currency_code', 'currency_symbol', 'language_code'];

    protected $hidden = [];

    protected $fileColumns = [
        'flag' => [
            'folder' => 'provinces',
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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

}
