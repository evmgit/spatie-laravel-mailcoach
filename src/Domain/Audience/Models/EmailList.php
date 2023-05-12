<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Database\Factories\EmailListFactory;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailList extends Model
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;

    public $guarded = [];

    public $table = 'mailcoach_email_lists';

    public $casts = [
        'requires_confirmation' => 'boolean',
        'allow_form_subscriptions' => 'boolean',
        'send_welcome_mail' => 'boolean',
        'welcome_mail_delay_in_minutes' => 'integer',
        'report_campaign_sent' => 'boolean',
        'report_campaign_summary' => 'boolean',
        'report_email_list_summary' => 'boolean',
        'email_list_summary_sent_at' => 'datetime',
    ];

    public function subscribers(): HasMany
    {
        return $this->allSubscribers()->subscribed();
    }

    public function allSubscribers(): HasMany
    {
        if (! (DB::connection() instanceof MySqlConnection)) {
            return $this->allSubscribersWithoutIndex();
        }

        $query = $this->hasMany(config('mailcoach.models.subscriber'), 'email_list_id')
            ->getQuery();

        $prefix = DB::getTablePrefix();

        $query = $query->from(DB::raw($prefix . $query->getQuery()->from . ' USE INDEX (email_list_subscribed_index)'));

        return $this->newHasMany(
            $query,
            $this,
            $this->getSubscriberTableName().'.email_list_id',
            'id'
        );
    }

    public function allSubscribersWithoutIndex(): HasMany
    {
        return $this->hasMany(config('mailcoach.models.subscriber'), 'email_list_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(config('mailcoach.models.campaign'), 'email_list_id');
    }

    public function subscriberImports(): HasMany
    {
        return $this->hasMany(SubscriberImport::class, 'email_list_id');
    }

    public function tags(): HasMany
    {
        return $this
            ->hasMany(Tag::class, 'email_list_id')
            ->orderBy('name');
    }

    public function segments()
    {
        return $this->hasMany(TagSegment::class, 'email_list_id');
    }

    public function scopeSummarySentMoreThanDaysAgo(Builder $query, int $days)
    {
        $query
            ->where('email_list_summary_sent_at', '<=', now()->subDays($days)->toDateTimeString());
    }

    public function allowedFormSubscriptionTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'mailcoach_email_list_allow_form_subscription_tags', 'email_list_id', 'tag_id')
            ->orderBy('name');
    }

    public function setFormExtraAttributesAttribute($value)
    {
        $this->attributes['allowed_form_extra_attributes'] = array_map('trim', explode(',', $value));
    }

    public function allowedFormExtraAttributes() : array
    {
        return explode(",", $this->allowed_form_extra_attributes);
    }

    public function subscribe(string $email, array $attributes = []): Subscriber
    {
        return $this->getSubscriberClass()::createWithEmail($email, $attributes)->subscribeTo($this);
    }

    public function subscribeSkippingConfirmation(string $email, array $attributes = []): Subscriber
    {
        return $this->getSubscriberClass()::createWithEmail($email, $attributes)->skipConfirmation()->subscribeTo($this);
    }

    public function isSubscribed(string $email): bool
    {
        if (! $subscriber = $this->getSubscriberClass()::findForEmail($email, $this)) {
            return false;
        }

        return $subscriber->isSubscribed();
    }

    public function unsubscribe(string $email): bool
    {
        if (! $subscriber = $this->getSubscriberClass()::findForEmail($email, $this)) {
            return false;
        }

        $subscriber->unsubscribe();

        return true;
    }

    public function getSubscriptionStatus(string $email): ?string
    {
        if (! $subscriber = $this->getSubscriberClass()::findForEmail($email, $this)) {
            return null;
        };

        return $subscriber->status;
    }

    public function feedUrl(): string
    {
        return route('mailcoach.feed', $this->uuid);
    }

    public function incomingFormSubscriptionsUrl(): string
    {
        return route('mailcoach.subscribe', $this->uuid);
    }

    public function welcomeMailableClass(): string
    {
        return empty($this->welcome_mailable_class)
            ? WelcomeMail::class
            : $this->welcome_mailable_class;
    }

    public function confirmSubscriberMailableClass(): string
    {
        return empty($this->confirmation_mailable_class)
            ? ConfirmSubscriberMail::class
            : $this->confirmation_mailable_class;
    }

    public function hasCustomizedWelcomeMailFields(): bool
    {
        if (! empty($this->welcome_mail_subject)) {
            return true;
        }

        if (! empty($this->welcome_mail_content)) {
            return true;
        }

        return false;
    }

    public function hasCustomizedConfirmationMailFields(): bool
    {
        if (! empty($this->confirmation_mail_subject)) {
            return true;
        }

        if (! empty($this->confirmation_mail_content)) {
            return true;
        }

        return false;
    }

    public function campaignReportRecipients(): array
    {
        if (empty($this->report_recipients)) {
            return [];
        }

        $recipients = explode(',', $this->report_recipients);

        return array_map('trim', $recipients);
    }

    public function summarize(CarbonInterface $summaryStartDateTime): array
    {
        return [
            'total_number_of_subscribers' => $this->subscribers()->count(),
            'total_number_of_subscribers_gained' => $this
                ->allSubscribers()
                ->where('subscribed_at', '>', $summaryStartDateTime->toDateTimeString())
                ->count(),
            'total_number_of_unsubscribes_gained' => $this
                ->allSubscribers()->unsubscribed()
                ->where('unsubscribed_at', '>', $summaryStartDateTime->toDateTimeString())
                ->count(),
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getEmailListClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): EmailListFactory
    {
        return new EmailListFactory();
    }
}
