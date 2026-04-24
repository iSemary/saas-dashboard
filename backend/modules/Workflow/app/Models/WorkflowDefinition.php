<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Workflow\Database\Factories\WorkflowDefinitionFactory;

class WorkflowDefinition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): WorkflowDefinitionFactory
    // {
    //     // return WorkflowDefinitionFactory::new();
    // }
}
