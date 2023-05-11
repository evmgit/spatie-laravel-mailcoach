<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use DOMElement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer as PersonalizedAutomationReplacer;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer as PersonalizedCampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

abstract class Sendable extends Model implements HasHtmlContent
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;
    use HasTemplate;

    protected $guarded = [];

    public $baseCasts = [
        'id' => 'int',
        'utm_tags' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'sent_at' => 'datetime',
        'requires_confirmation' => 'boolean',
        'statistics_calculated_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'last_modified_at' => 'datetime',
        'mailable_arguments' => 'array',
    ];

    abstract public function links(): HasMany;

    abstract public function clicks(): HasManyThrough;

    abstract public function opens(): HasManyThrough|HasMany;

    abstract public function sends(): HasMany;

    abstract public function unsubscribes(): HasMany;

    abstract public function bounces(): HasManyThrough;

    abstract public function complaints(): HasManyThrough;

    abstract public function isReady(): bool;

    public function getTemplateFieldValues(): array
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        return $structuredHtml['templateValues'] ?? [];
    }

    public function setTemplateFieldValues(array $fieldValues = []): self
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        $structuredHtml['templateValues'] = $fieldValues;

        $this->structured_html = json_encode($structuredHtml);

        return $this;
    }

    public function hasValidHtml(): bool
    {
        return (new HtmlRule())->passes('html', $this->html);
    }

    public function htmlError(): ?string
    {
        $rule = new HtmlRule();

        if ($rule->passes('html', $this->html)) {
            return null;
        }

        return $rule->message();
    }

    public function getCasts()
    {
        return array_merge($this->baseCasts, $this->casts ?? []);
    }

    public function htmlContainsUnsubscribeUrlPlaceHolder(): bool
    {
        return Str::contains($this->html, '::unsubscribeUrl::') || Str::contains($this->html, '::preferencesUrl::');
    }

    public function from(string $email, string $name = null)
    {
        $this->update([
            'from_email' => $email,
            'from_name' => $name,
        ]);

        return $this;
    }

    public function replyTo(string $email, string $name = null)
    {
        $this->update([
            'reply_to_email' => $email,
            'reply_to_name' => $name,
        ]);

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->ensureUpdatable();

        $this->update(compact('subject'));

        return $this;
    }

    public function getFromEmail(?Send $send = null): string
    {
        return $this->from_email
            ?? $this->emailList?->default_from_email
            ?? $send?->subscriber->emailList->default_from_email
            ?? config('mail.from.address');
    }

    public function getFromName(?Send $send = null): ?string
    {
        return $this->from_name
            ?? $this->emailList?->default_from_name
            ?? $send?->subscriber->emailList->default_from_name
            ?? config('mail.from.name');
    }

    public function getReplyToEmail(?Send $send = null): ?string
    {
        return $this->reply_to_email
            ?? $this->emailList?->default_reply_to_email
            ?? $send?->subscriber->emailList->default_reply_to_email
            ?? null;
    }

    public function getReplyToName(?Send $send = null): ?string
    {
        return $this->reply_to_name
            ?? $this->emailList?->default_reply_to_name
            ?? $send?->subscriber->emailList->default_reply_to_name
            ?? null;
    }

    public function utmTags(bool $bool = true): self
    {
        $this->ensureUpdatable();

        $this->update(['utm_tags' => $bool]);

        return $this;
    }

    /* TODO create SendableMail */
    abstract public function useMailable(string $mailableClass, array $mailableArguments = []): self;

    public function content(string $html): self
    {
        $this->ensureUpdatable();

        $this->update(compact('html'));

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

    protected function ensureSendable()
    {
    }

    public function wasAlreadySentToSubscriber(Subscriber $subscriber): bool
    {
        return $this->sends()->whereNotNull('sent_at')->where('subscriber_id', $subscriber->id)->exists();
    }

    abstract public function sendTestMail(string|array $emails): void;

    abstract public function webviewUrl(): string;

    public function getReplacers(): Collection
    {
        return match (true) {
            $this instanceof Campaign => collect(config('mailcoach.campaigns.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof CampaignReplacer || $class instanceof PersonalizedCampaignReplacer),
            $this instanceof AutomationMail => collect(config('mailcoach.automation.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof AutomationMailReplacer || $class instanceof PersonalizedAutomationReplacer),
            default => collect(),
        };
    }

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    public function sendsCount(): int
    {
        return $this->sends()->whereNotNull('sent_at')->count();
    }

    protected function ensureUpdatable(): void
    {
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

    public function htmlLinks(): Collection
    {
        if ($this->getHtml() === '') {
            return collect();
        }

        $dom = app(CreateDomDocumentFromHtmlAction::class)->execute($this->getHtml());

        return collect($dom->getElementsByTagName('a'))
            ->map(function (DOMElement $link) {
                return $link->getAttribute('href');
            })->reject(function (string $url) {
                return str_contains($url, '::');
            })
            ->reject(fn (string $url) => empty($url))
            ->unique();
    }

    public function getHtml(): string
    {
        return $this->html ?? '';
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function getStructuredHtml(): string
    {
        return $this->structured_html ?? '';
    }

    public function hasTemplates(): bool
    {
        return true;
    }

    public function sizeInKb(): int
    {
        return (int) ceil(mb_strlen($this->getHtml(), '8bit') / 1000);
    }
}
