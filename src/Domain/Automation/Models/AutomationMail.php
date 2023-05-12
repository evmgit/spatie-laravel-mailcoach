<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Mailcoach\Database\Factories\AutomationMailFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailTestJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;

class AutomationMail extends Sendable
{
    public $table = 'mailcoach_automation_mails';

    public function links(): HasMany
    {
        return $this->hasMany(static::getAutomationMailLinkClass(), 'automation_mail_id');
    }

    public function opens(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                static::getAutomationMailOpenClass(),
                $this->getSendClass(),
                'automation_mail_id'
            )
            ->orderBy('created_at');
    }

    public function clicks(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                static::getAutomationMailClickClass(),
                $this->getSendClass(),
                'automation_mail_id'
            )
            ->orderBy('created_at');
    }

    public function sends(): HasMany
    {
        return $this->hasMany($this->getSendClass(), 'automation_mail_id');
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(static::getAutomationMailUnsubscribeClass(), 'automation_mail_id');
    }

    public function bounces(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, $this->getSendClass(), 'automation_mail_id')
            ->where('type', SendFeedbackType::BOUNCE);
    }

    public function complaints(): HasManyThrough
    {
        return $this
            ->hasManyThrough(SendFeedbackItem::class, $this->getSendClass(), 'automation_mail_id')
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

        return true;
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->ensureUpdatable();

        if (! is_a($mailableClass, MailcoachMail::class, true)) {
            throw CouldNotSendAutomationMail::invalidMailableClass($this, $mailableClass);
        }

        $this->update(['mailable_class' => $mailableClass, 'mailable_arguments' => $mailableArguments]);

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

    public function send(Subscriber $subscriber): self
    {
        $this->ensureSendable();

        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();

            $this->content($this->contentFromMailable());
        }

        dispatch(new SendAutomationMailToSubscriberJob($this, $subscriber));

        return $this;
    }

    public function wasAlreadySentToSubscriber(Subscriber $subscriber): bool
    {
        return $this
            ->sends()
            ->whereNotNull('sent_at')
            ->where('subscriber_id', $subscriber->id)
            ->exists();
    }

    public function sendTestMail(string | array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch(new SendAutomationMailTestJob($this, $email));
        });
    }

    public function webviewUrl(): string
    {
        return url(route('mailcoach.automations.webview', $this->uuid));
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

    public function hasCustomMailable(): bool
    {
        if ($this->mailable_class === MailcoachMail::class) {
            return false;
        }

        return ! is_null($this->mailable_class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getAutomationMailClass()::where($field, $value)->firstOrFail();
    }

    public function fromEmail(Subscriber $subscriber): string
    {
        return $this->from_email ?? $subscriber->emailList->default_from_email ?? config('mail.from.address');
    }

    public function fromName(Subscriber $subscriber): ?string
    {
        return $this->from_name ?? $subscriber->emailList->default_from_name ?? config('mail.from.name');
    }

    public function replyToEmail(Subscriber $subscriber): ?string
    {
        return $this->reply_to_email ?? $subscriber->emailList->default_reply_to_email;
    }

    public function replyToName(Subscriber $subscriber): ?string
    {
        return $this->reply_to_name ?? $subscriber->emailList->default_reply_to_name;
    }

    protected static function newFactory(): AutomationMailFactory
    {
        return new AutomationMailFactory();
    }
}
