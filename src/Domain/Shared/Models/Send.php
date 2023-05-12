<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Events\ComplaintRegisteredEvent;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Actions\StripUtmTagsFromUrlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailOpenedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

class Send extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_sends';

    public $guarded = [];

    public $dates = [
        'sent_at',
        'failed_at',
    ];

    public function concernsCampaign(): bool
    {
        return ! is_null($this->campaign_id);
    }

    public function concernsAutomationMail(): bool
    {
        return ! is_null($this->automation_mail_id);
    }

    public function concernsTransactionalMail(): bool
    {
        return ! is_null($this->transactional_mail_id);
    }

    public static function findByTransportMessageId(string $transportMessageId): ?Model
    {
        return static::where('transport_message_id', $transportMessageId)->first();
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.subscriber'), 'subscriber_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.campaign'), 'campaign_id');
    }

    public function automationMail(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationMailClass(), 'automation_mail_id');
    }

    public function transactionalMail(): BelongsTo
    {
        return $this->belongsTo(static::getTransactionalMailClass(), 'transactional_mail_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(static::getCampaignOpenClass(), 'send_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(static::getCampaignClickClass(), 'send_id');
    }

    public function automationMailOpens(): HasMany
    {
        return $this->hasMany(static::getAutomationMailOpenClass(), 'send_id');
    }

    public function automationMailClicks(): HasMany
    {
        return $this->hasMany(static::getAutomationMailClickClass(), 'send_id');
    }

    public function transactionalMailOpens(): HasMany
    {
        return $this->hasMany(TransactionalMailOpen::class, 'send_id');
    }

    public function transactionalMailClicks(): HasMany
    {
        return $this->hasMany(TransactionalMailClick::class, 'send_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(SendFeedbackItem::class, 'send_id');
    }

    public function latestFeedback(): ?SendFeedbackItem
    {
        return $this->feedback()->latest()->first();
    }

    public function bounces(): HasMany
    {
        return $this
            ->hasMany(SendFeedbackItem::class)
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasMany
    {
        return $this
            ->hasMany(SendFeedbackItem::class)
            ->where('type', SendFeedbackType::COMPLAINT);
    }

    public function markAsSent()
    {
        $this->sent_at = now();

        $this->save();

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return ! is_null($this->sent_at);
    }

    public function storeTransportMessageId(string $transportMessageId)
    {
        $this->update(['transport_message_id' => $transportMessageId]);

        return $this;
    }

    public function registerOpen(?DateTimeInterface $openedAt = null): CampaignOpen | AutomationMailOpen | TransactionalMailOpen | null
    {
        if ($this->concernsCampaign()) {
            return $this->registerCampaignOpen($openedAt);
        }

        if ($this->concernsAutomationMail()) {
            return $this->registerAutomationMailOpen($openedAt);
        }

        if ($this->concernsTransactionalMail()) {
            return $this->registerTransactionalMailOpen($openedAt);
        }

        return null;
    }

    public function registerCampaignOpen(?DateTimeInterface $openedAt = null): ?CampaignOpen
    {
        if (! $this->campaign->track_opens) {
            return null;
        }

        if ($this->wasOpenedInTheLastSeconds($this->opens(), 5)) {
            return null;
        }

        $campaignOpen = static::getCampaignOpenClass()::create([
            'send_id' => $this->id,
            'campaign_id' => $this->campaign->id,
            'subscriber_id' => $this->subscriber->id,
            'created_at' => $openedAt ?? now(),
        ]);

        event(new CampaignOpenedEvent($campaignOpen));

        $this->campaign->dispatchCalculateStatistics();

        return $campaignOpen;
    }

    public function registerAutomationMailOpen(?DateTimeInterface $openedAt = null): ?AutomationMailOpen
    {
        if (! $this->automationMail->track_opens) {
            return null;
        }

        if ($this->wasOpenedInTheLastSeconds($this->automationMailOpens(), 5)) {
            return null;
        }

        $automationMailOpen = static::getAutomationMailOpenClass()::create([
            'send_id' => $this->id,
            'automation_mail_id' => $this->automationMail->id,
            'subscriber_id' => $this->subscriber->id,
            'created_at' => $openedAt ?? now(),
        ]);

        event(new AutomationMailOpenedEvent($automationMailOpen));

        return $automationMailOpen;
    }

    public function registerTransactionalMailOpen(?DateTimeInterface $openedAt = null): ?TransactionalMailOpen
    {
        if (! $this->transactionalMail->track_opens) {
            return null;
        }

        if ($this->wasOpenedInTheLastSeconds($this->transactionalMailOpens(), 5)) {
            return null;
        }

        $transactionalMailOpen = TransactionalMailOpen::create([
            'send_id' => $this->id,
            'created_at' => $openedAt ?? now(),
        ]);

        event(new TransactionalMailOpenedEvent($transactionalMailOpen));

        return $transactionalMailOpen;
    }

    protected function wasOpenedInTheLastSeconds(HasMany $relation, int $seconds): bool
    {
        $latestOpen = $relation->latest()->first();

        if (! $latestOpen) {
            return false;
        }

        return $latestOpen->created_at->diffInSeconds() < $seconds;
    }

    public function registerClick(string $url, ?DateTimeInterface $clickedAt = null): CampaignClick | AutomationMailClick | TransactionalMailClick | null
    {
        $url = resolve(StripUtmTagsFromUrlAction::class)->execute($url);

        if ($this->concernsCampaign()) {
            return $this->registerCampaignClick($url, $clickedAt);
        }

        if ($this->concernsAutomationMail()) {
            return $this->registerAutomationMailClick($url, $clickedAt);
        }

        if ($this->concernsTransactionalMail()) {
            return $this->registerTransactionalMailClick($url, $clickedAt);
        }

        return null;
    }

    protected function registerCampaignClick(string $url, ?DateTimeInterface $clickedAt = null): ?CampaignClick
    {
        if (! $this->campaign->track_clicks) {
            return null;
        }

        if (Str::startsWith($url, route('mailcoach.unsubscribe', ''))) {
            return null;
        }

        $campaignLink = static::getCampaignLinkClass()::firstOrCreate([
            'campaign_id' => $this->campaign->id,
            'url' => $url,
        ]);

        $campaignClick = $campaignLink->registerClick($this, $clickedAt);

        event(new CampaignLinkClickedEvent($campaignClick));

        $this->campaign->dispatchCalculateStatistics();

        return $campaignClick;
    }

    protected function registerAutomationMailClick(string $url, ?DateTimeInterface $clickedAt = null): ?AutomationMailClick
    {
        if (! $this->automationMail->track_clicks) {
            return null;
        }

        if (Str::startsWith($url, route('mailcoach.unsubscribe', ''))) {
            return null;
        }

        /** @var AutomationMailLink $automationMailLink */
        $automationMailLink = static::getAutomationMailLinkClass()::firstOrCreate([
            'automation_mail_id' => $this->automationMail->id,
            'url' => $url,
        ]);

        $automationMailLink = $automationMailLink->registerClick($this, $clickedAt);

        event(new AutomationMailLinkClickedEvent($automationMailLink));

        $this->automationMail->dispatchCalculateStatistics();

        return $automationMailLink;
    }

    protected function registerTransactionalMailClick(string $url, ?DateTimeInterface $clickedAt = null): ?TransactionalMailClick
    {
        if (! $this->transactionalMail->track_clicks) {
            return null;
        }

        $transactionalMailClick = TransactionalMailClick::create([
            'send_id' => $this->id,
            'url' => $url,
        ]);

        event(new TransactionalMailLinkClickedEvent($transactionalMailClick));

        return $transactionalMailClick;
    }

    public function registerBounce(?DateTimeInterface $bouncedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::BOUNCE,
            'created_at' => $bouncedAt ?? now(),
        ]);

        optional($this->subscriber)->unsubscribe($this);

        event(new BounceRegisteredEvent($this));

        return $this;
    }

    public function registerComplaint(?DateTimeInterface $complainedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::COMPLAINT,
            'created_at' => $complainedAt ?? now(),
        ]);

        optional($this->subscriber)->unsubscribe($this);

        event(new ComplaintRegisteredEvent($this));

        return $this;
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('sent_at');
    }

    public function scopeSent(Builder $query): void
    {
        $query
            ->whereNotNull('sent_at')
            ->whereNull('failed_at');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->whereNotNull('failed_at');
    }

    public function scopeBounced(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::BOUNCE);
        });
    }

    public function scopeComplained(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::COMPLAINT);
        });
    }

    public function markAsFailed(string $failureReason): self
    {
        $this->update([
            'sent_at' => now(),
            'failed_at' => now(),
            'failure_reason' => $failureReason,
        ]);

        return $this;
    }

    public function prepareRetryAfterFailedSend()
    {
        $this->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getSendClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): SendFactory
    {
        return new SendFactory();
    }
}
