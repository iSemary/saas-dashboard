<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpensePolicy extends Model
{
    use SoftDeletes;

    protected $table = 'exp_policies';

    protected $fillable = [
        'name', 'description', 'type', 'rules',
        'is_active', 'priority', 'created_by', 'custom_fields',
    ];

    protected $casts = [
        'rules' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'custom_fields' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority');
    }
}
