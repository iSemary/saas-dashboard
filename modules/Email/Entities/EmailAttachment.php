<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailAttachment extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;
    protected $connection = "landlord";

    public $singleTitle = "email_attachment";
    public $pluralTitle = "email_attachments";

    protected $fillable = [
        'campaign_id',
        'template_id',
        'file_id',
    ];
}
