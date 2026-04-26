<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SmContactList extends Model
{
    use SoftDeletes;

    protected $table = 'sm_contact_lists';

    protected $fillable = [
        'name', 'description', 'status', 'contacts_count', 'created_by',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(SmContact::class, 'sm_contact_list_members', 'contact_list_id', 'contact_id');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(SmCampaign::class, 'sm_campaign_lists', 'contact_list_id', 'campaign_id');
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
