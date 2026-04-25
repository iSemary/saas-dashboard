<?php

namespace Modules\StaticPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\StaticPages\Database\Factories\StaticPageFactory;

class StaticPage extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'is_public' => 'boolean',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(StaticPage::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StaticPage::class, 'parent_id')->orderBy('order');
    }

    public function attributes()
    {
        return $this->hasMany(StaticPageAttribute::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeRootPages($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsPublicAttribute()
    {
        return $this->is_public;
    }

    public function getIsDraftAttribute()
    {
        return $this->status === 'draft';
    }

    public function getIsInactiveAttribute()
    {
        return $this->status === 'inactive';
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function getFullPathAttribute()
    {
        $path = [$this->slug];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->slug);
            $parent = $parent->parent;
        }

        return implode('/', $path);
    }

    // Methods
    public function getAttributeByKey($key, $languageCode = 'en')
    {
        return $this->attributes()
            ->where('key', $key)
            ->where('language_code', $languageCode)
            ->where('status', 'active')
            ->first();
    }

    public function getAttributeValue($key, $languageCode = 'en', $default = null)
    {
        $attribute = $this->getAttributeByKey($key, $languageCode);
        return $attribute ? $attribute->value : $default;
    }

    public function setAttributeValue($key, $value, $languageCode = 'en', $metadata = null)
    {
        return $this->attributes()->updateOrCreate(
            [
                'key' => $key,
                'language_code' => $languageCode,
            ],
            [
                'value' => $value,
                'status' => 'active',
                'metadata' => $metadata,
            ]
        );
    }

    public function getTranslatedAttributes($languageCode = 'en')
    {
        return $this->attributes()
            ->where('language_code', $languageCode)
            ->where('status', 'active')
            ->get()
            ->keyBy('key');
    }

    public function getAllTranslations()
    {
        return $this->attributes()
            ->where('status', 'active')
            ->get()
            ->groupBy('language_code');
    }

    public function publish()
    {
        $this->update(['status' => 'active']);
    }

    public function unpublish()
    {
        $this->update(['status' => 'inactive']);
    }

    public function makeDraft()
    {
        $this->update(['status' => 'draft']);
    }

    public function incrementRevision()
    {
        $this->increment('revision');
    }

    public function getSeoData()
    {
        return [
            'title' => $this->meta_title ?: $this->name,
            'description' => $this->meta_description ?: $this->description,
            'keywords' => $this->meta_keywords,
            'image' => $this->image,
        ];
    }

    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumbs, [
                'name' => $current->name,
                'slug' => $current->slug,
                'url' => $current->full_path,
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    protected static function newFactory(): StaticPageFactory
    {
        return StaticPageFactory::new();
    }
}
