<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class UserMeta extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id', 'meta_key', 'meta_value'];

    public function getConnectionName()
    {
        $currentConnection = config('database.default');

        if ($currentConnection == 'landlord') {
            return 'landlord';
        }

        return 'tenant';
    }

    public function getByMetaKey($userId, $metaKey)
    {
        return self::where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
