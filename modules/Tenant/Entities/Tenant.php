<?php

namespace Modules\Tenant\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $connection = 'landlord';
    
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 
        'domain', 
        'database', 
    ];
}
