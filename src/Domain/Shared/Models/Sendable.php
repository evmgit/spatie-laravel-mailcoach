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
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

abstract class Sendable extends Model implements HasHtmlContent
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    protected $guarded = [];

    public $baseCasts = [
        'id' => 'int',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
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

    abstract public function opens(): HasManyThrough | HasMany;

    abstract public function sends(): HasMany;

    abstract public function unsubscribes(): HasMany;

    abstract public function bounces(): HasManyThrough;

    abstract public function complaints(): HasManyThrough;

    abstract public function isReady(): bool;

    public function hasValidHtml(): bool
    {
        return (new HtmlRule())->passes('html', $this->html);
    }

    public function getCasts()
    {
        return array_merge($this->baseCasts, $this->casts ?? []);
    }

    public function htmlContainsUnsubscribeUrlPlaceHolder(): bool
    {
        return Str::contains($this->html, '::unsubscribeUrl::');
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

    public function trackOpens(bool $bool = true): self
    {
        $this->ensureUpdatable();

        $this->update(['track_opens' => $bool]);

        return $this;
    }

    public function trackClicks(bool $bool = true): self
    {
        $this->ensureUpdatable();

        $this->update(['track_clicks' => $bool]);

        return $this;
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

    public function sendTestMail(string | array $emails): void
    {
        if ($this->hasCustomMailable()) {
            $this->pullSubjectFromMailable();
        }

        collect($emails)->each(function (string $email) {
            dispatch(new SendCampaignTestJob($this, $email));
        });
    }

    abstract public function webviewUrl(): string;

    public function getMailable(): MailcoachMail
    {
        $mailableClass = $this->mailable_class ?? MailcoachMail::class;
        $mailableArguments = $this->mailable_arguments ?? [];

        return resolve($mailableClass, $mailableArguments);
    }

    /** TODO: verify if this function can be removed */
    public function dispatchCalculateStatistics()
    {
        $lock = new CalculateStatisticsLock($this);

        if (! $lock->get()) {
            return;
        }

        dispatch(new CalculateStatisticsJob($this));
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
            });
    }

    public function getHtml(): string
    {
        return $this->html ?? '';
    }

    public function getStructuredHtml(): string
    {
        return $this->structured_html ?? '';
    }

    public function sizeInKb(): int
    {
        return (int) ceil(mb_strlen($this->getHtml(), '8bit') / 1000);
    }
}
