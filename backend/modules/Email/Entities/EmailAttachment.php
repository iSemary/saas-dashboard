<?php

namespace Modules\Email\Entities;

use App\Helpers\FileHelper;
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
        'email_campaign_id',
        'email_template_log_id',
        'file_id',
    ];
}
