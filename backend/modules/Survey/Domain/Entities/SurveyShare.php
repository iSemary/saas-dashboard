<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Auth\Entities\User;
use Modules\Survey\Domain\ValueObjects\ShareChannel;
use Modules\Survey\Domain\Exceptions\ShareExpiredException;

class SurveyShare extends Model
{
    protected $table = 'survey_shares';

    protected $fillable = [
        'survey_id',
        'channel',
        'token',
        'config',
        'max_uses',
        'uses_count',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($share) {
            if (empty($share->token)) {
                $share->token = $share->generateToken();
            }
        });
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'share_id');
    }

    public function incrementUses(): void
    {
        $this->increment('uses_count');
    }

    public function isExpired(): bool
    {
        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return true;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return true;
        }

        return false;
    }

    public function checkValid(): void
    {
        if ($this->isExpired()) {
            throw new ShareExpiredException($this->token);
        }
    }

    public function getPublicUrl(): string
    {
        return url('/public/survey/' . $this->token);
    }

    public function getEmbedCode(): string
    {
        $width = $this->config['width'] ?? '100%';
        $height = $this->config['height'] ?? '600px';

        return sprintf(
            '<iframe src="%s" width="%s" height="%s" frameborder="0"></iframe>',
            $this->getPublicUrl(),
            $width,
            $height
        );
    }

    public function getQrCodeUrl(): string
    {
        // Will be implemented with QR generation library
        return url('/qr/survey/' . $this->token);
    }

    public function generateToken(): string
    {
        return Str::random(32);
    }

    public function getChannelLabel(): string
    {
        return ShareChannel::fromString($this->channel)->label();
    }

    public function getChannelIcon(): string
    {
        return ShareChannel::fromString($this->channel)->icon();
    }

    public function isLinkChannel(): bool
    {
        return $this->channel === ShareChannel::LINK->value;
    }

    public function isEmbedChannel(): bool
    {
        return $this->channel === ShareChannel::EMBED->value;
    }
}
