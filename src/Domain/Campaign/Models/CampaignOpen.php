<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\CampaignOpenFactory;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignOpen extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_campaign_opens';

    protected $guarded = [];

    protected $casts = [
        'first_opened_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(self::getSendClass(), 'send_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(self::getCampaignClass(), 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }

    protected static function newFactory(): CampaignOpenFactory
    {
        return new CampaignOpenFactory();
    }
}
