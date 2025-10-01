<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Workflow\Database\Factories\WorkflowInstanceFactory;

class WorkflowInstance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): WorkflowInstanceFactory
    // {
    //     // return WorkflowInstanceFactory::new();
    // }
}
