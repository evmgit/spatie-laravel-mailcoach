<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\Steps\ThrottlingStepComponent;

class SendinblueSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::sendinblue-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::sendinblue-summary-step' => [
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
        Livewire::component('mailcoach::sendinblue-configuration', SendinblueSetupWizardComponent::class);

        Livewire::component('mailcoach::sendinblue-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::sendinblue-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::sendinblue-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::sendinblue-summary-step', SummaryStepComponent::class);
    }
}
