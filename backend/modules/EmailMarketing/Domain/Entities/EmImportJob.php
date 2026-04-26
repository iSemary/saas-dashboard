<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmImportJob extends Model
{
    use SoftDeletes;

    protected $table = 'em_import_jobs';

    protected $fillable = [
        'contact_list_id', 'file_path', 'column_mapping', 'status',
        'total_rows', 'processed_rows', 'failed_rows', 'errors', 'created_by',
    ];

    protected $casts = [
        'column_mapping' => 'array',
        'errors' => 'array',
    ];

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(EmContactList::class, 'contact_list_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}
