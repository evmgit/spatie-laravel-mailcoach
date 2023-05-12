<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Database\Factories\TransactionalMailTemplateFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\RenderTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\InvalidTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class TransactionalMailTemplate extends Model implements HasHtmlContent
{
    public $table = 'mailcoach_transactional_mail_templates';

    use HasFactory;
    use UsesMailcoachModels;

    public $guarded = [];

    public $casts = [
        'store_mail' => 'boolean',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'from' => 'string',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'replacers' => 'array',
    ];

    public function isValid(): bool
    {
        try {
            $this->validate();
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function validate(): void
    {
        $mailable = $this->getMailable();

        $mailable->render();
    }

    public function getMailable(): Mailable
    {
        $mailableClass = $this->test_using_mailable;

        if (! class_exists($mailableClass)) {
            throw InvalidTemplate::mailableClassNotFound($this);
        }

        $traits = class_uses_recursive($mailableClass);

        if (! in_array(UsesMailcoachTemplate::class, $traits)) {
            throw InvalidTemplate::mailableClassNotValid($this);
        }

        return $mailableClass::testInstance();
    }

    public function canBeTested(): bool
    {
        return ! is_null($this->test_using_mailable);
    }

    public function replacers(): Collection
    {
        return collect($this->replacers ?? [])
            ->map(function (string $replacerName): TransactionalMailReplacer {
                $replacerClass = config("mailcoach.transactional.replacers.{$replacerName}");

                if (is_null($replacerClass)) {
                    throw InvalidTemplate::replacerNotFound($this, $replacerName);
                }

                if (! is_a($replacerClass, TransactionalMailReplacer::class, true)) {
                    throw InvalidTemplate::invalidReplacer($this, $replacerName, $replacerClass);
                }

                return resolve($replacerClass);
            });
    }

    public function render(Mailable $mailable): string
    {
        $action = Config::getTransactionalActionClass('render_template', RenderTemplateAction::class);

        return $action->execute($this, $mailable);
    }

    public function getHtml(): ?string
    {
        return $this->body;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    public function toString(): string
    {
        return implode(',', $this->to ?? []);
    }

    public function ccString(): string
    {
        return implode(',', $this->cc ?? []);
    }

    public function bccString(): string
    {
        return implode(',', $this->bcc ?? []);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getTransactionalMailTemplateClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): TransactionalMailTemplateFactory
    {
        return new TransactionalMailTemplateFactory();
    }
}
