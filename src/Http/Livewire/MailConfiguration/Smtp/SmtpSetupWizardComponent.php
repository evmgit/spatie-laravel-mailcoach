<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Smtp;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Smtp\Steps\SmtpSettingsStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Smtp\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Smtp\Steps\ThrottlingStepComponent;

class SmtpSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::smtp-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::smtp-summary-step' => [
                'mailerId' => $this->mailer->id,
            ],
        ];
    }

    public function steps(): array
    {
        return [
            SmtpSettingsStepComponent::class,
            ThrottlingStepComponent::class,
            SummaryStepComponent::class,
        ];
    }

    public static function registerLivewireComponents(): void
    {
        Livewire::component('mailcoach::smtp-configuration', SmtpSetupWizardComponent::class);

        Livewire::component('mailcoach::smtp-settings-step', SmtpSettingsStepComponent::class);
        Livewire::component('mailcoach::smtp-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::smtp-summary-step', SummaryStepComponent::class);
    }
}
