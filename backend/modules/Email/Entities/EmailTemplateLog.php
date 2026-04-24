<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateLog extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    public $singleTitle = "email_template_log";
    public $pluralTitle = "email_template_logs";

    protected $fillable = [
        'name',
        'subject',
        'body',
    ];
}
