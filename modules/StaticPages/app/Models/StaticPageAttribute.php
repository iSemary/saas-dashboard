<?php

namespace Modules\StaticPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\StaticPages\Database\Factories\StaticPageAttributeFactory;

class StaticPageAttribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'static_page_id',
        'key',
        'value',
        'language_code',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function staticPage()
    {
        return $this->belongsTo(StaticPage::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public function scopeByLanguage($query, $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }

    public function scopeByPage($query, $pageId)
    {
        return $query->where('static_page_id', $pageId);
    }

    public function scopeTranslatable($query)
    {
        return $query->whereIn('key', ['content', 'title', 'subtitle', 'description', 'meta_title', 'meta_description']);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsTranslatableAttribute()
    {
        return in_array($this->key, ['content', 'title', 'subtitle', 'description', 'meta_title', 'meta_description']);
    }

    public function getFormattedValueAttribute()
    {
        // Handle different types of content
        switch ($this->key) {
            case 'content':
                return $this->value; // HTML content
            case 'title':
            case 'subtitle':
            case 'meta_title':
                return strip_tags($this->value); // Plain text
            case 'description':
            case 'meta_description':
                return strip_tags($this->value); // Plain text
            default:
                return $this->value;
        }
    }

    // Methods
    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    public function updateValue($value, $metadata = null)
    {
        $this->update([
            'value' => $value,
            'metadata' => $metadata ?: $this->metadata,
        ]);
    }

    public function getTranslationForLanguage($languageCode)
    {
        return static::where('static_page_id', $this->static_page_id)
            ->where('key', $this->key)
            ->where('language_code', $languageCode)
            ->where('status', 'active')
            ->first();
    }

    public function getAllTranslations()
    {
        return static::where('static_page_id', $this->static_page_id)
            ->where('key', $this->key)
            ->where('status', 'active')
            ->get()
            ->groupBy('language_code');
    }

    public function hasTranslation($languageCode)
    {
        return static::where('static_page_id', $this->static_page_id)
            ->where('key', $this->key)
            ->where('language_code', $languageCode)
            ->where('status', 'active')
            ->exists();
    }

    public function getContentLength()
    {
        return strlen(strip_tags($this->value));
    }

    public function getWordCount()
    {
        return str_word_count(strip_tags($this->value));
    }

    public function getReadingTime()
    {
        $wordCount = $this->getWordCount();
        $averageReadingSpeed = 200; // words per minute
        return ceil($wordCount / $averageReadingSpeed);
    }

    protected static function newFactory(): StaticPageAttributeFactory
    {
        return StaticPageAttributeFactory::new();
    }
}
