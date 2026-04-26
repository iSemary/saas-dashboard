<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EmailMarketing\Domain\ValueObjects\EmProviderType;

class EmCredential extends Model
{
    use SoftDeletes;

    protected $table = 'em_credentials';

    protected $fillable = [
        'name', 'provider', 'host', 'port', 'username', 'password', 'encryption',
        'from_email', 'from_name', 'api_key', 'region', 'is_default', 'status', 'created_by',
    ];

    protected $hidden = ['password', 'api_key'];

    protected $casts = [
        'provider' => EmProviderType::class,
        'is_default' => 'boolean',
        'port' => 'integer',
    ];

    public function campaigns()
    {
        return $this->hasMany(EmCampaign::class, 'credential_id');
    }
}
