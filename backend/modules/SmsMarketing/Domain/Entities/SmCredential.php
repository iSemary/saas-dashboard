<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SmsMarketing\Domain\ValueObjects\SmProviderType;

class SmCredential extends Model
{
    use SoftDeletes;

    protected $table = 'sm_credentials';

    protected $fillable = [
        'name', 'provider', 'account_sid', 'auth_token', 'from_number',
        'webhook_url', 'is_default', 'status', 'created_by',
    ];

    protected $hidden = ['auth_token'];

    protected $casts = [
        'provider' => SmProviderType::class,
        'is_default' => 'boolean',
    ];

    public function campaigns()
    {
        return $this->hasMany(SmCampaign::class, 'credential_id');
    }
}
