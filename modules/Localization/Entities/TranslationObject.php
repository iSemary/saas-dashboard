<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TranslationObject extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = ['object_type', 'object_id', 'translation_id'];

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'translation_id');
    }
}
