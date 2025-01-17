<?php

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    public $singleTitle = "notification";
    public $pluralTitle = "notifications";

    protected $fillable = [

        'user_id',
        // TODO remove this and add object type and object id and remove route
        'module_id',
        'name',
        'description',
        'type', // info, alert, announcement
        'route',
        'priority', // 'low', 'medium', 'high'
        'icon', // image
        'metadata',
        'seen_at',
    ];

    protected $imageColumns = [
        'icon' => [
            'folder' => 'notifications',
            'is_encrypted' => false,
            'access_level' => 'public',
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIconAttribute($value)
    {
        return $this->getFileUrl($value);
    }
}
