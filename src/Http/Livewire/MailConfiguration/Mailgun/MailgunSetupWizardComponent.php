<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps\ThrottlingStepComponent;

class MailgunSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::mailgun-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::mailgun-summary-step' => [
                'mailerId' => $this->mailer->id,
            ],
        ];
    }

    public function steps(): array
    {
        return [
            AuthenticationStepComponent::class,
            ThrottlingStepComponent::class,
            FeedbackStepComponent::class,
            SummaryStepComponent::class,
        ];
    }

    public static function registerLivewireComponents(): void
    {
        Livewire::component('mailcoach::mailgun-configuration', MailgunSetupWizardComponent::class);

        Livewire::component('mailcoach::mailgun-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::mailgun-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::mailgun-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::mailgun-summary-step', SummaryStepComponent::class);
    }
}
