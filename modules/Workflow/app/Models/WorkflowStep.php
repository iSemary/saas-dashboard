<?php

namespace Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Workflow\Database\Factories\WorkflowStepFactory;

class WorkflowStep extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): WorkflowStepFactory
    // {
    //     // return WorkflowStepFactory::new();
    // }
}
