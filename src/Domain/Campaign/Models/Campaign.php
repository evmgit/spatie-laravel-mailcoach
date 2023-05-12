<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotUpdateCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\CanBeScheduled;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Campaign extends Sendable implements Feedable
{
    use CanBeScheduled;
    use SendsToSegment;

    public $table = 'mailcoach_campaigns';

    public $casts = [
        'sent_to_number_of_subscribers' => 'integer',
        'scheduled_at' => 'datetime',
        'campaigns_feed_enabled' => 'boolean',
        'all_jobs_added_to_batch_at' => 'datetime',
        'summary_mail_sent_at' => 'datetime',
    ];

    public static function booted()
    {
        static::creating(function (Campaign $campaign) {
            if (! $campaign->status) {
                $campaign->status = CampaignStatus::DRAFT;
            }
        });
    }

    public static function scopeSentBetween(Builder $query, CarbonInterface $periodStart, CarbonInterface $periodEnd): void
    {
        $query
            ->where('sent_at', '>=', $periodStart)
            ->where('sent_at', '<', $periodEnd);
    }

    public function scopeDraft(Builder $query): void
    {
        $query
            ->where('status', CampaignStatus::DRAFT)
            ->whereNull('scheduled_at')
            ->orderBy('created_at');
    }

    public function scopeSendingOrSent(Builder $query): void
    {
        $query->whereIn('status', [CampaignStatus::SENDING, CampaignStatus::SENT]);
    }

    public function scopeNeedsSummaryToBeReported(Builder $query)
    {
        $query
            ->whereHas(
                'emailList',
                fn (Builder $query) => $query->where('report_campaign_summary', true)
            )
            ->whereNull('summary_mail_sent_at');
    }

    public function scopeSent(Builder $query): void
    {
        $query->where('status', CampaignStatus::SENT);
    }

    public function scopeSentDaysAgo(Builder $query, int $daysAgo)
    {
        $query
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now()->subDays($daysAgo)->toDateTimeString());
    }

    public function links(): HasMany
    {
        return $this->hasMany(static::getCampaignLinkClass(), 'campaign_id');
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(static::getCampaignClickClass(), static::getCampaignLinkClass(), 'campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(static::getCampaignOpenClass(), 'campaign_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany($this->getSendClass(), 'campaign_id');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(static::getCampaignUnsubscribeClass(), 'campaign_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, $this->getSendClass(), 'campaign_id')
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, $this->getSendClass(), 'campaign_id')
            ->where('type', SendFeedbackType::COMPLAINT);
    }

    public function isReady(): bool
    {
        if (! $this->html) {
            return false;
        }

        if (! $this->hasValidHtml()) {
            return false;
        }

        if (! $this->subject) {
            return false;
        }

        if (! optional($this->emailList)->default_from_email) {
            return false;
        }

        if (! $this->emailListSubscriberCount()) {
            return false;
        }

        return true;
    }

    public function isPending(): bool
    {
        return ! in_array($this->status, [
            CampaignStatus::SENDING,
            CampaignStatus::SENT,
        ]);
    }

    public function isScheduled(): bool
    {
        return $this->isDraft() && $this->scheduled_at;
    }

    public function isEditable(): bool
    {
        if ($this->isSending()) {
            return false;
        }

        if ($this->isSent()) {
            return false;
        }

        return true;
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->ensureUpdatable();

        if (! is_a($mailableClass, MailcoachMail::class, true)) {
            throw CouldNotSendCampaign::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass, 'mailable_arguments' => $mailableArguments]);

        return $this;
    }

    public function to(EmailList $emailList): self
    {
        $this->ensureUpdatable();

        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function contentFromMailable(): string
    {
        return $this
            ->getMailable()
            ->setSendable($this)
            ->render();
    }

    public function pullSubjectFromMailable(): void
    {
        if (! $this->hasCustomMailable()) {
            return;
        }

        $mailable = $this->getMailable()->setSendable($this);
        $mailable->build();

        if (! empty($mailable->subject)) {
            $this->subject($mailable->subject);
        }
    }

    public function send(): self
    {
        $this->ensureSendable();

        if (empty($this->from_email)) {
            $this->from_email = $this->emailList->default_from_email;
            $this->save();
        }

        if (empty($this->from_name)) {
            $this->from_name = $this->emailList->default_from_name;
            $this->save();
        }

        if (empty($this->reply_to_email)) {
            $this->reply_to_email = $this->emailList->default_reply_to_email;
            $this->save();
        }

        if (empty($this->reply_to_name)) {
            $this->reply_to_name = $this->emailList->default_reply_to_name;
            $this->save();
        }

        $this->update([
            'segment_description' => $this->getSegment()->description(),
            'last_modified_at' => now(),
        ]);

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        $this->markAsSending();

        dispatch(new SendCampaignJob($this));

        return $this;
    }

    public function sendTo(EmailList $emailList): self
    {
        return $this->to($emailList)->send();
    }

    protected function ensureSendable()
    {
        if ($this->isSending()) {
            throw CouldNotSendCampaign::beingSent($this);
        }

        if ($this->isSent()) {
            throw CouldNotSendCampaign::alreadySent($this);
        }

        if ($this->isCancelled()) {
            throw CouldNotSendCampaign::cancelled($this);
        }

        if (is_null($this->emailList)) {
            throw CouldNotSendCampaign::noListSet($this);
        }

        if ($this->hasCustomMailable()) {
            return;
        }

        if (empty($this->from_email) && empty($this->emailList->default_from_email)) {
            throw CouldNotSendCampaign::noFromEmailSet($this);
        }

        if (empty($this->subject)) {
            throw CouldNotSendCampaign::noSubjectSet($this);
        }

        if (empty($this->html)) {
            throw CouldNotSendCampaign::noContent($this);
        }
    }

    public function markAsSent(int $numberOfSubscribers): self
    {
        $this->update([
            'status' => CampaignStatus::SENT,
            'sent_at' => now(),
            'statistics_calculated_at' => now(),
            'sent_to_number_of_subscribers' => $numberOfSubscribers,
        ]);

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return $this->isSent();
    }

    public function wasAlreadySentToSubscriber(Subscriber $subscriber): bool
    {
        return $this->sends()->whereNotNull('sent_at')->where('subscriber_id', $subscriber->id)->exists();
    }

    public function sendTestMail(string | array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch(new SendCampaignTestJob($this, $email));
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.campaign.webview', $this->uuid));
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    public function dispatchCalculateStatistics()
    {
        $lock = new CalculateStatisticsLock($this);

        if (! $lock->get()) {
            return;
        }

        $latestEvent = max(
            $this->sends()->latest()->first()?->created_at,
            $this->opens()->latest()->first()?->created_at,
            $this->clicks()->latest()->first()?->created_at,
        );

        if (! $this->statistics_calculated_at || ($latestEvent && $latestEvent >= $this->statistics_calculated_at)) {
            dispatch(new CalculateStatisticsJob($this));
        }
    }

    public function toFeedItem(): FeedItem
    {
        return (new FeedItem())
            ->authorName('Mailcoach')
            ->authorEmail($this->from_email)
            ->link($this->webviewUrl())
            ->title($this->subject)
            ->id($this->uuid)
            ->summary('')
            ->updated($this->sent_at);
    }

    public function emailListSubscriberCount(): int
    {
        if (! $this->emailList) {
            return 0;
        }

        return $this->emailList->subscribers()->count();
    }

    public function sendsCount(): int
    {
        return $this->sends()->whereNotNull('sent_at')->count();
    }

    public function wasSentToAllSubscribers(): bool
    {
        if (! $this->isSent()) {
            return false;
        }

        return $this->sends()->pending()->count() === 0;
    }

    protected function ensureUpdatable(): void
    {
        if ($this->isSending()) {
            throw CouldNotUpdateCampaign::beingSent($this);
        }

        if ($this->isSent()) {
            throw CouldNotSendCampaign::alreadySent($this);
        }

        if ($this->isCancelled()) {
            throw CouldNotSendCampaign::cancelled($this);
        }
    }

    protected function markAsSending(): self
    {
        $this->update([
            'status' => CampaignStatus::SENDING,
        ]);

        return $this;
    }

    public function hasTroublesSendingOutMails(): bool
    {
        if ($this->status !== CampaignStatus::SENDING) {
            return false;
        }

        if (! $this->last_modified_at) {
            return false;
        }

        if ($this->last_modified_at->diffInHours() < 1) {
            return false;
        }

        $latestSend = $this
            ->sends()
            ->whereNotNull('sent_at')
            ->orderByDesc('sent_at')
            ->first();

        if ($latestSend->sent_at->diffInHours() < 1) {
            return false;
        }

        return true;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatus::DRAFT;
    }

    public function isSending(): bool
    {
        return $this->status == CampaignStatus::SENDING;
    }

    public function isSent(): bool
    {
        return $this->status == CampaignStatus::SENT;
    }

    public function isSendingOrSent(): bool
    {
        return $this->isSending() || $this->isSent();
    }

    public function isCancelled(): bool
    {
        return $this->status == CampaignStatus::CANCELLED;
    }

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === MailcoachMail::class) {
            return false;
        }

        return ! is_null($this->mailable_class);
    }

    public function htmlWithInlinedCss(): string
    {
        $html = $this->getHtml();

        if ($this->hasCustomMailable()) {
            $html = $this->contentFromMailable();
        }

        return (new CssToInlineStyles())->convert($html);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getCampaignClass()::where($field, $value)->firstOrFail();
    }

    public function getBatchName(): string
    {
        return Str::slug("{$this->name} ({$this->id})");
    }

    protected static function newFactory(): CampaignFactory
    {
        return new CampaignFactory();
    }
}
