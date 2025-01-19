<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailTemplate extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "email_template";
    public $pluralTitle = "email_templates";

    protected $fillable = [
        'name',
        'subject',
        'body',
        'status',
    ];
}
