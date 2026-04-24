<?php

namespace Modules\StaticPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\StaticPages\Database\Factories\LanguageFactory;

class Language extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'native_name',
        'flag',
        'is_active',
        'is_default',
        'direction',
        'date_format',
        'time_format',
        'currency_code',
        'locale_settings',
        'sort_order',
        'custom_fields',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'locale_settings' => 'array',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function staticPageAttributes()
    {
        return $this->hasMany(StaticPageAttribute::class, 'language_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeLtr($query)
    {
        return $query->where('direction', 'ltr');
    }

    public function scopeRtl($query)
    {
        return $query->where('direction', 'rtl');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->is_active;
    }

    public function getIsDefaultAttribute()
    {
        return $this->is_default;
    }

    public function getIsRtlAttribute()
    {
        return $this->direction === 'rtl';
    }

    public function getIsLtrAttribute()
    {
        return $this->direction === 'ltr';
    }

    public function getDisplayNameAttribute()
    {
        return $this->native_name ?: $this->name;
    }

    public function getFlagEmojiAttribute()
    {
        return $this->flag ?: '🌐';
    }

    // Methods
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function setAsDefault()
    {
        // Remove default from other languages
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    public function getLocaleSettings()
    {
        return array_merge([
            'direction' => $this->direction,
            'date_format' => $this->date_format,
            'time_format' => $this->time_format,
            'currency_code' => $this->currency_code,
        ], $this->locale_settings ?? []);
    }

    public function getFormattedDate($date)
    {
        if (!$date) {
            return null;
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format($this->date_format);
    }

    public function getFormattedTime($time)
    {
        if (!$time) {
            return null;
        }

        if (is_string($time)) {
            $time = \Carbon\Carbon::parse($time);
        }

        return $time->format($this->time_format);
    }

    public function getFormattedDateTime($datetime)
    {
        if (!$datetime) {
            return null;
        }

        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }

        return $datetime->format($this->date_format . ' ' . $this->time_format);
    }

    public function getTranslationCount()
    {
        return $this->staticPageAttributes()->count();
    }

    public function getActiveTranslationCount()
    {
        return $this->staticPageAttributes()->where('status', 'active')->count();
    }

    public function getCompletionPercentage()
    {
        $totalAttributes = StaticPageAttribute::where('status', 'active')->count();
        $translatedAttributes = $this->getActiveTranslationCount();
        
        if ($totalAttributes === 0) {
            return 0;
        }
        
        return round(($translatedAttributes / $totalAttributes) * 100, 2);
    }

    public function getMissingTranslations()
    {
        $allKeys = StaticPageAttribute::where('status', 'active')
            ->distinct('key')
            ->pluck('key');
            
        $translatedKeys = $this->staticPageAttributes()
            ->where('status', 'active')
            ->distinct('key')
            ->pluck('key');
            
        return $allKeys->diff($translatedKeys);
    }

    public static function getDefaultLanguage()
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    public static function getActiveLanguages()
    {
        return static::where('is_active', true)->ordered()->get();
    }

    public static function getLanguageByCode($code)
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    protected static function newFactory(): LanguageFactory
    {
        return LanguageFactory::new();
    }
}
