<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExpenseTag extends Model
{
    use SoftDeletes;

    protected $table = 'exp_tags';

    protected $fillable = [
        'name', 'color', 'created_by', 'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function expenses(): BelongsToMany
    {
        return $this->belongsToMany(Expense::class, 'exp_expense_tag', 'tag_id', 'expense_id');
    }
}
