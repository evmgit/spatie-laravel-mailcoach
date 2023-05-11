<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationMailUnsubscribe extends Model
{
    use HasUuid;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_mail_unsubscribes';

    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(self::getCampaignClass(), 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }
}
