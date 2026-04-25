<?php

namespace Modules\Utilities\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class StaticPage extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "static_page";
    public $pluralTitle = "static_pages";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'body',
        'status',
        'type',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_public',
        'author_id',
        'revision',
        'order',
        'parent_id',
        'custom_fields',
    ];

    public function attributes()
    {
        return $this->hasMany(StaticPageAttribute::class, 'static_page_id');
    }

    /**
     * Get attribute value by key and language
     *
     * @param string $key
     * @param string $languageCode
     * @return string
     */
    public function getAttributeValue($key, $languageCode = 'en')
    {
        $attribute = $this->attributes()
            ->where('key', $key)
            ->where('language_code', $languageCode)
            ->where('status', 'active')
            ->first();

        return $attribute ? $attribute->value : '';
    }
}
