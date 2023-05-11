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
use Spatie\Mailcoach\Domain\Shared\Actions\StripUtmTagsFromUrlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailOpenedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;
use Spatie\Mailcoach\Mailcoach;

class Send extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_sends';

    public $guarded = [];

    public $dates = [
        'sending_job_dispatched_at',
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
        return ! is_null($this->transactional_mail_log_item_id);
    }

    public function getSendable(): ?Sendable
    {
        if ($this->concernsCampaign()) {
            return $this->campaign;
        }

        if ($this->concernsAutomationMail()) {
            return $this->automationMail;
        }

        return null;
    }

    public function getMailerKey(): ?string
    {
        if ($this->concernsAutomationMail()) {
            return $this->subscriber->emailList->automation_mailer
                ?? Mailcoach::defaultAutomationMailer();
        }

        if ($this->concernsCampaign()) {
            return $this->campaign->getMailerKey();
        }

        return Mailcoach::defaultTransactionalMailer();
    }

    public static function findByTransportMessageId(string $transportMessageId): ?Model
    {
        return static::where('transport_message_id', $transportMessageId)->first();
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(self::getCampaignClass(), 'campaign_id');
    }

    public function automationMail(): BelongsTo
    {
        return $this->belongsTo(self::getAutomationMailClass(), 'automation_mail_id');
    }

    public function transactionalMailLogItem(): BelongsTo
    {
        return $this->belongsTo(self::getTransactionalMailLogItemClass(), 'transactional_mail_log_item_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getCampaignOpenClass(), 'send_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(self::getCampaignClickClass(), 'send_id');
    }

    public function automationMailOpens(): HasMany
    {
        return $this->hasMany(self::getAutomationMailOpenClass(), 'send_id');
    }

    public function automationMailClicks(): HasMany
    {
        return $this->hasMany(self::getAutomationMailClickClass(), 'send_id');
    }

    public function transactionalMailOpens(): HasMany
    {
        return $this->hasMany(self::getTransactionalMailOpenClass(), 'send_id');
    }

    public function transactionalMailClicks(): HasMany
    {
        return $this->hasMany(self::getTransactionalMailClickClass(), 'send_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(self::getSendFeedbackItemClass(), 'send_id');
    }

    public function latestFeedback(): ?SendFeedbackItem
    {
        return $this->feedback()->latest()->first();
    }

    public function bounces(): HasMany
    {
        return $this
            ->hasMany(self::getSendFeedbackItemClass())
            ->where('type', SendFeedbackType::Bounce);
    }

    public function complaints(): HasMany
    {
        return $this
            ->hasMany(self::getSendFeedbackItemClass())
            ->where('type', SendFeedbackType::Complaint);
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

    public function markAsSendingJobDispatched(): self
    {
        $this->update([
            'sending_job_dispatched_at' => now(),
        ]);

        return $this;
    }

    public function mailSendingJobWasDispatched(): bool
    {
        return ! is_null($this->sending_job_dispatched_at);
    }

    public function storeTransportMessageId(string $transportMessageId)
    {
        $this->update(['transport_message_id' => $transportMessageId]);

        return $this;
    }

    public function registerOpen(?DateTimeInterface $openedAt = null): CampaignOpen|AutomationMailOpen|TransactionalMailOpen|null
    {
        if ($this->concernsTransactionalMail()) {
            return $this->registerTransactionalMailOpen($openedAt);
        }

        if (! $this->subscriber) {
            return null;
        }

        if ($this->concernsCampaign()) {
            return $this->registerCampaignOpen($openedAt);
        }

        if ($this->concernsAutomationMail()) {
            return $this->registerAutomationMailOpen($openedAt);
        }

        return null;
    }

    public function registerCampaignOpen(?DateTimeInterface $openedAt = null): ?CampaignOpen
    {
        if ($this->wasOpenedInTheLastSeconds($this->opens(), 5)) {
            return null;
        }

        if (! $this->campaign) {
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
        if ($this->wasOpenedInTheLastSeconds($this->automationMailOpens(), 5)) {
            return null;
        }

        if (! $this->automationMail) {
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
        if ($this->wasOpenedInTheLastSeconds($this->transactionalMailOpens(), 5)) {
            return null;
        }

        $transactionalMailOpen = self::getTransactionalMailOpenClass()::create([
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

    public function registerClick(string $url, ?DateTimeInterface $clickedAt = null): CampaignClick|AutomationMailClick|TransactionalMailClick|null
    {
        $url = resolve(StripUtmTagsFromUrlAction::class)->execute($url);

        if ($this->concernsTransactionalMail()) {
            return $this->registerTransactionalMailClick($url, $clickedAt);
        }

        if (! $this->subscriber) {
            return null;
        }

        if ($this->concernsCampaign() && $this->campaign) {
            return $this->registerCampaignClick($url, $clickedAt);
        }

        if ($this->concernsAutomationMail() && $this->automationMail) {
            return $this->registerAutomationMailClick($url, $clickedAt);
        }

        return null;
    }

    protected function registerCampaignClick(string $url, ?DateTimeInterface $clickedAt = null): ?CampaignClick
    {
        if (Str::startsWith($url, route('mailcoach.unsubscribe', ''))) {
            return null;
        }

        $campaignLink = self::getCampaignLinkClass()::firstOrCreate([
            'campaign_id' => $this->campaign->id,
            'url' => $url,
        ], ['uuid' => Str::uuid()]);

        $campaignClick = $campaignLink->registerClick($this, $clickedAt);

        event(new CampaignLinkClickedEvent($campaignClick));

        $this->campaign->dispatchCalculateStatistics();

        return $campaignClick;
    }

    protected function registerAutomationMailClick(string $url, ?DateTimeInterface $clickedAt = null): ?AutomationMailClick
    {
        if (Str::startsWith($url, route('mailcoach.unsubscribe', ''))) {
            return null;
        }

        /** @var AutomationMailLink $automationMailLink */
        $automationMailLink = self::getAutomationMailLinkClass()::firstOrCreate([
            'automation_mail_id' => $this->automationMail->id,
            'url' => $url,
        ], ['uuid' => Str::uuid()]);

        $automationMailLink = $automationMailLink->registerClick($this, $clickedAt);

        event(new AutomationMailLinkClickedEvent($automationMailLink));

        $this->automationMail->dispatchCalculateStatistics();

        return $automationMailLink;
    }

    protected function registerTransactionalMailClick(string $url, ?DateTimeInterface $clickedAt = null): ?TransactionalMailClick
    {
        $transactionalMailClick = self::getTransactionalMailClickClass()::create([
            'send_id' => $this->id,
            'url' => $url,
            'created_at' => $clickedAt ?? now(),
        ]);

        event(new TransactionalMailLinkClickedEvent($transactionalMailClick));

        return $transactionalMailClick;
    }

    public function registerBounce(?DateTimeInterface $bouncedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::Bounce,
            'uuid' => Str::uuid(),
            'created_at' => $bouncedAt ?? now(),
        ]);

        optional($this->subscriber)->unsubscribe($this);

        event(new BounceRegisteredEvent($this));

        return $this;
    }

    public function registerComplaint(?DateTimeInterface $complainedAt = null)
    {
        $this->feedback()->create([
            'type' => SendFeedbackType::Complaint,
            'uuid' => Str::uuid(),
            'created_at' => $complainedAt ?? now(),
        ]);

        optional($this->subscriber)->unsubscribe($this);

        event(new ComplaintRegisteredEvent($this));

        return $this;
    }

    public function scopeUndispatched(Builder $query): void
    {
        $query->whereNull('sending_job_dispatched_at');
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

    public function scopeInvalidated(Builder $query): void
    {
        $query->whereNotNull('invalidated_at');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->whereNotNull('failed_at');
    }

    public function scopeBounced(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::Bounce);
        });
    }

    public function scopeComplained(Builder $query): void
    {
        $query->whereHas('feedback', function (Builder $query) {
            $query->where('type', SendFeedbackType::Complaint);
        });
    }

    public function invalidate(): self
    {
        $this->update([
            'sent_at' => now(),
            'invalidated_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed(string $failureReason): self
    {
        if (! $this->exists) {
            return $this;
        }

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
            'sending_job_dispatched_at' => now(),
        ]);
    }

    protected static function newFactory(): SendFactory
    {
        return new SendFactory();
    }
}
