<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\ValidateCampaignRequirementsAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotUpdateCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\CanBeScheduled;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Mailcoach;
use Throwable;
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
        'add_subscriber_tags' => 'boolean',
        'add_subscriber_link_tags' => 'boolean',
        'all_sends_created_at' => 'datetime',
        'all_sends_dispatched_at' => 'datetime',
        'summary_mail_sent_at' => 'datetime',
        'status' => CampaignStatus::class,
        'show_publicly' => 'boolean',
    ];

    public static function booted()
    {
        static::creating(function (Campaign $campaign) {
            if (! $campaign->status) {
                $campaign->status = CampaignStatus::Draft;
            }
        });
    }

    public static function scopeSentBetween(Builder $query, CarbonInterface $periodStart, CarbonInterface $periodEnd): void
    {
        $query
            ->whereNotNull('sent_at')
            ->whereNotNull('all_sends_dispatched_at')
            ->whereNotNull('all_sends_created_at')
            ->where('sent_at', '>=', $periodStart)
            ->where('sent_at', '<', $periodEnd);
    }

    public function scopeDraft(Builder $query): void
    {
        $query
            ->where('status', CampaignStatus::Draft)
            ->whereNull('scheduled_at')
            ->orderBy('created_at');
    }

    public function scopeSendingOrSent(Builder $query): void
    {
        $query->whereIn('status', [CampaignStatus::Sending, CampaignStatus::Sent]);
    }

    public function scopeShowPublicly(Builder $query): void
    {
        $query
            ->sendingOrSent()
            ->where('show_publicly', true);
    }

    public function scopeSending(Builder $query): void
    {
        $query->where('status', CampaignStatus::Sending);
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
        $query->where('status', CampaignStatus::Sent);
    }

    public function scopeSentDaysAgo(Builder $query, int $daysAgo)
    {
        $query
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now()->subDays($daysAgo)->toDateTimeString());
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo($this->getTemplateClass());
    }

    public function links(): HasMany
    {
        return $this->hasMany(static::getCampaignLinkClass(), 'campaign_id');
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(self::getCampaignClickClass(), self::getCampaignLinkClass(), 'campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getCampaignOpenClass(), 'campaign_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(self::getSendClass(), 'campaign_id');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(self::getCampaignUnsubscribeClass(), 'campaign_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(self::getSendFeedbackItemClass(), self::getSendClass(), 'campaign_id')
            ->where('type', SendFeedbackType::Bounce);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(self::getSendFeedbackItemClass(), self::getSendClass(), 'campaign_id')
            ->where('type', SendFeedbackType::Complaint);
    }

    /**
     * Returns a tuple with open & click tracking values
     *
     * @return array
     */
    public function tracking(): array
    {
        $mailer = $this->getMailer();

        if (! $this->emailList) {
            return [null, null];
        }

        return [
            $mailer?->get('open_tracking_enabled', false),
            $mailer?->get('click_tracking_enabled', false),
        ];
    }

    public function getMailerKey(): ?string
    {
        return $this->emailList->campaign_mailer
            ?? Mailcoach::defaultCampaignMailer();
    }

    public function getMailer(): ?Mailer
    {
        $mailerClass = config('mailcoach.models.mailer');

        if (! class_exists($mailerClass)) {
            return null;
        }

        if (! $mailerKey = $this->getMailerKey()) {
            return null;
        }

        return $mailerClass::all()->first(fn ($mailerModel) => $mailerModel->configName() === $mailerKey);
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

        if (! $this->getMailerKey()) {
            return false;
        }

        return count($this->validateRequirements()) === 0;
    }

    public function validateRequirements(): array
    {
        /** @var ValidateCampaignRequirementsAction $action */
        $action = Mailcoach::getCampaignActionClass('validate_campaign_requirements', ValidateCampaignRequirementsAction::class);

        return $action->execute($this);
    }

    public function isPending(): bool
    {
        return ! in_array($this->status, [
            CampaignStatus::Sending,
            CampaignStatus::Sent,
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

        if ($this->isCancelled()) {
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
        }

        if (empty($this->from_name)) {
            $this->from_name = $this->emailList->default_from_name;
        }

        if (empty($this->reply_to_email)) {
            $this->reply_to_email = $this->emailList->default_reply_to_email;
        }

        if (empty($this->reply_to_name)) {
            $this->reply_to_name = $this->emailList->default_reply_to_name;
        }

        $this->segment_description = $this->getSegment()->description();
        $this->last_modified_at = now();
        $this->save();

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        $this->markAsSending();

        return $this;
    }

    public function cancel(): self
    {
        $this->update([
            'status' => CampaignStatus::Cancelled,
            'sent_at' => now(),
        ]);

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

        if (count($this->validateRequirements()) > 0) {
            throw CouldNotSendCampaign::requirementsNotMet($this);
        }
    }

    public function markAsSent(int $numberOfSubscribers): self
    {
        $this->update([
            'status' => CampaignStatus::Sent,
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

    public function sendTestMail(string|array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch_sync(new SendCampaignTestJob($this, $email));
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

    public function dispatchCalculateStatistics(): void
    {
        if (! $this->isSendingOrSent() && ! $this->isCancelled()) {
            return;
        }

        dispatch(new CalculateStatisticsJob($this));
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

    public function sendsWithoutInvalidated(): HasMany
    {
        return $this->sends()->whereNull('invalidated_at');
    }

    public function sendsWithErrors(): HasMany
    {
        return $this->sends()->whereNotNull('failed_at');
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
            'status' => CampaignStatus::Sending,
        ]);

        return $this;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatus::Draft;
    }

    public function isPreparing(): bool
    {
        return $this->isSending() && ! $this->sent_to_number_of_subscribers;
    }

    public function isSending(): bool
    {
        return $this->status == CampaignStatus::Sending;
    }

    public function isSent(): bool
    {
        return $this->status == CampaignStatus::Sent;
    }

    public function isSendingOrSent(): bool
    {
        return $this->isSending() || $this->isSent();
    }

    public function isCancelled(): bool
    {
        return $this->status == CampaignStatus::Cancelled;
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

    public function getSummary(): string
    {
        $html = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $this->webview_html);

        $converter = new HtmlConverter([
            'strip_tags' => true,
            'suppress_errors' => false,
            'remove_nodes' => 'head script style img code hr',
        ]);

        try {
            $text = $converter->convert($html);
            $text = app(RenderMarkdownToHtmlAction::class)->execute($text);
        } catch (Throwable) {
            $text = $html;
        }

        $text = strip_tags($text, ['p', 'strong', 'em', 'b', 'i', 'br']);

        $text = preg_replace('/=+/', '', $text);

        return Str::limit($text, 300);
    }

    public function allSendsCreated(): bool
    {
        return ! is_null($this->all_sends_created_at);
    }

    public function markAsAllSendsCreated(): self
    {
        $this->update([
            'all_sends_created_at' => now(),
        ]);

        return $this;
    }

    public function allMailSendingJobsDispatched(): bool
    {
        return ! is_null($this->all_sends_dispatched_at);
    }

    public function markAsAllMailSendingJobsDispatched(): self
    {
        $this->update([
            'all_sends_dispatched_at' => now(),
        ]);

        return $this;
    }

    protected static function newFactory(): CampaignFactory
    {
        return new CampaignFactory();
    }

    public function getFieldContent(string $fieldName): string
    {
        return $this->fields?->get($fieldName) ?? '';
    }

    public function websiteUrl(): string
    {
        return route('mailcoach.website.campaign', [$this->emailList->website_slug, $this->uuid]);
    }

    public function websiteSummary(): ?string
    {
        if (! $this->webview_html) {
            return null;
        }

        $document = app(CreateDomDocumentFromHtmlAction::class)->execute($this->webview_html);

        $preheader = $document->getElementById('preheader');

        if (! $preheader) {
            return null;
        }

        return $preheader->textContent;
    }
}
