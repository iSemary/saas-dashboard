<?php

namespace Modules\Reporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Reporting\Database\Factories\DashboardFactory;

class Dashboard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DashboardFactory
    // {
    //     // return DashboardFactory::new();
    // }
}
