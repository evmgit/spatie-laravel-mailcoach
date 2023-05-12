<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\CampaignLinkFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignLink extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_campaign_links';

    public $casts = [
        'click_count' => 'integer',
        'unique_click_count' => 'integer',
    ];

    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.campaign'), 'campaign_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(static::getCampaignClickClass());
    }

    public function registerClick(Send $send, ?DateTimeInterface $clickedAt = null): CampaignClick
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick $campaignClick */
        $campaignClick = $this->clicks()->create([
            'send_id' => $send->id,
            'subscriber_id' => $send->subscriber->id,
            'created_at' => $clickedAt ?? now(),
        ]);

        $numberOfTimesClickedBySubscriber = $this->clicks()
            ->where('subscriber_id', $send->subscriber->id)
            ->count();

        if ($numberOfTimesClickedBySubscriber === 1) {
            $this->increment('unique_click_count');
        }

        $this->increment('click_count');

        return $campaignClick;
    }

    protected static function newFactory(): CampaignLinkFactory
    {
        return new CampaignLinkFactory();
    }
}
