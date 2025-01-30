<?php

namespace Modules\Development\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DatabaseFlow extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "database_flow";
    public $pluralTitle = "database_flow";

    protected $fillable = ['connection', 'table', 'position', 'color'];

    protected $casts = [
        'position' => 'array',
    ];
}
