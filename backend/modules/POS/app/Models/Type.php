<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'pos_types';

    protected $fillable = [
        'en_name',
        'ar_name',
        'model',
    ];
}
