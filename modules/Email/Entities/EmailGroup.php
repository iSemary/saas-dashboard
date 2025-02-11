<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailGroup extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "email_group";
    public $pluralTitle = "email_groups";

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function recipients()
    {
        return $this->hasMany(EmailRecipientGroup::class, 'email_group_id');
    }
}
