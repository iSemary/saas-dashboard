<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailCredential extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "email_credential";
    public $pluralTitle = "email_credentials";
    protected $fillable = [
        'name',
        'description',
        'from_address',
        'from_name',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'status',
    ];
}
