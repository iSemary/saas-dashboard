<?php

namespace Modules\Auth\Entities;

use Laravel\Passport\Token as PassportToken;

class OAuthAccessToken extends PassportToken
{
    public function getConnectionName()
    {
        $currentConnection = config('database.default');
        if ($currentConnection == 'landlord') {
            return 'landlord';
        }
        return 'tenant';
    }
}
