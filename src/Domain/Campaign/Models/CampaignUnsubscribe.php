<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\CampaignUnsubscribeFactory;

class CampaignUnsubscribe extends Model
{
    use HasFactory;

    public $table = 'mailcoach_campaign_unsubscribes';

    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.campaign'), 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.subscriber'), 'subscriber_id');
    }

    protected static function newFactory(): CampaignUnsubscribeFactory
    {
        return new CampaignUnsubscribeFactory();
    }
}
