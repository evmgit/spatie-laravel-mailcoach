<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses\Steps\ThrottlingStepComponent;

class SesSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::ses-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::ses-summary-step' => [
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
        Livewire::component('mailcoach::ses-configuration', SesSetupWizardComponent::class);
        Livewire::component('mailcoach::ses-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::ses-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::ses-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::ses-summary-step', SummaryStepComponent::class);
    }
}
