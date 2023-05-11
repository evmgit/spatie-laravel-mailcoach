<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps\ThrottlingStepComponent;

class SendGridSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::sendgrid-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::sendgrid-summary-step' => [
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
        Livewire::component('mailcoach::sendgrid-configuration', SendGridSetupWizardComponent::class);

        Livewire::component('mailcoach::sendgrid-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::sendgrid-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::sendgrid-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::sendgrid-summary-step', SummaryStepComponent::class);
    }
}
