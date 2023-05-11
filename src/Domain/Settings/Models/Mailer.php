<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Settings\Enums\MailerTransport;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Mailer extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesMailcoachModels;

    public $table = 'mailcoach_mailers';

    public $guarded = [];

    public $casts = [
        'default' => 'boolean',
        'transport' => MailerTransport::class,
        'configuration' => AsEncryptedArrayObject::class,
        'ready_for_use' => 'boolean',
    ];

    protected static function booted()
    {
        self::creating(function (Mailer $mailer) {
            $name = strtolower($mailer->name);
            $mailer->config_key_name = Str::slug("mailcoach-{$name}");
        });
    }

    public static function registerAllConfigValues(): void
    {
        /** @var \Illuminate\Support\Collection<\Spatie\Mailcoach\Domain\Settings\Models\Mailer> $mailers */
        $mailers = self::getMailerClass()::all()->where('ready_for_use', true);

        $mailers->each(fn (Mailer $mailer) => $mailer->registerConfigValues());
        $defaultMailer = $mailers->where('default', true)->first() ?? $mailers->first();

        config()->set('mailcoach.mailer', $defaultMailer?->configName());
    }

    public function registerConfigValues()
    {
        if (! $this->ready_for_use) {
            return;
        }

        switch ($this->transport->value) {
            case MailerTransport::Ses->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'ses',
                    'key' => $this->get('ses_key'),
                    'secret' => $this->get('ses_secret'),
                    'region' => $this->get('ses_region'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                config()->set('mailcoach.ses_feedback.configuration_set', $this->get('ses_configuration_set'));

                break;
            case MailerTransport::SendGrid->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'sendgrid',
                    'key' => $this->get('apiKey'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                config()->set('mailcoach.sendgrid_feedback.signing_secret', $this->get('signing_secret'));

                break;
            case MailerTransport::Sendinblue->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'sendinblue',
                    'key' => $this->get('apiKey'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                config()->set('mailcoach.sendinblue_feedback.signing_secret', $this->get('signing_secret'));

                break;
            case MailerTransport::Smtp->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'smtp',
                    'host' => $this->get('host'),
                    'username' => $this->get('username'),
                    'password' => $this->get('password'),
                    'encryption' => $this->get('encryption'),
                    'port' => $this->get('port'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                break;
            case MailerTransport::Postmark->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'postmark',
                    'token' => $this->get('apiKey'),
                    'message_stream_id' => $this->get('streamId'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                config()->set('mailcoach.postmark_feedback.signing_secret', $this->get('signing_secret'));

                break;
            case MailerTransport::Mailgun->value:
                config()->set("mail.mailers.{$this->configName()}", [
                    'transport' => 'mailgun',
                    'domain' => $this->get('domain'),
                    'secret' => $this->get('apiKey'),
                    'endpoint' => $this->get('baseUrl'),
                    'timespan_in_seconds' => $this->get('timespan_in_seconds'),
                    'mails_per_timespan' => $this->get('mails_per_timespan'),
                ]);

                config()->set('mailcoach.mailgun_feedback.signing_secret', $this->get('signing_secret'));

                break;
        }
    }

    public static function findByConfigKeyName(string $configKeyName): ?self
    {
        return self::where('config_key_name', $configKeyName)->first();
    }

    public function configName(): string
    {
        return $this->config_key_name;
    }

    public function isReadyForUse(): bool
    {
        return $this->ready_for_use;
    }

    public function get(string $configurationKey, ?string $default = null)
    {
        return Arr::get($this->configuration, $configurationKey) ?? $default;
    }

    public function merge(array $values): self
    {
        $newValues = array_merge($this->configuration?->toArray() ?? [], $values);

        $this->update(['configuration' => $newValues]);

        return $this;
    }

    public function markAsReadyForUse(): self
    {
        $this->update(['ready_for_use' => true]);

        return $this;
    }
}
