<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmContactList extends Model
{
    use SoftDeletes;

    protected $table = 'em_contact_lists';

    protected $fillable = [
        'name', 'description', 'status', 'contacts_count', 'created_by',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(EmContact::class, 'em_contact_list_members', 'contact_list_id', 'contact_id');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(EmCampaign::class, 'em_campaign_lists', 'contact_list_id', 'campaign_id');
    }

    public function incrementContactsCount(): void
    {
        $this->increment('contacts_count');
    }

    public function decrementContactsCount(): void
    {
        $this->decrement('contacts_count');
    }
}
