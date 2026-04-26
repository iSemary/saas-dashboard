<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Customer\Entities\Tenant\Brand;

class Announcement extends Model
{
    use SoftDeletes;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    protected $table = 'hr_announcements';

    protected $fillable = [
        'title',
        'body',
        'audience',
        'department_ids',
        'starts_at',
        'ends_at',
        'requires_acknowledgment',
        'attachments',
        'created_by',
        'brand_id',
    ];

    protected $casts = [
        'department_ids' => 'array',
        'attachments' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'requires_acknowledgment' => 'boolean',
    ];
}
