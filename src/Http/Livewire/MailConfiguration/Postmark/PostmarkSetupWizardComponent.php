<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark;

use Livewire\Livewire;
use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps\AuthenticationStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps\FeedbackStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps\MessageStreamStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps\SummaryStepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps\ThrottlingStepComponent;

class PostmarkSetupWizardComponent extends WizardComponent
{
    public Mailer $mailer;

    public function mount()
    {
        if ($this->mailer->isReadyForUse()) {
            $this->currentStepName = 'mailcoach::postmark-summary-step';
        }
    }

    public function initialState(): ?array
    {
        return [
            'mailcoach::postmark-summary-step' => [
                'mailerId' => $this->mailer->id,
            ],
        ];
    }

    public function steps(): array
    {
        return [
            AuthenticationStepComponent::class,
            MessageStreamStepComponent::class,
            ThrottlingStepComponent::class,
            FeedbackStepComponent::class,
            SummaryStepComponent::class,
        ];
    }

    public static function registerLivewireComponents(): void
    {
        Livewire::component('mailcoach::postmark-configuration', PostmarkSetupWizardComponent::class);

        Livewire::component('mailcoach::postmark-authentication-step', AuthenticationStepComponent::class);
        Livewire::component('mailcoach::postmark-message-stream-step', MessageStreamStepComponent::class);
        Livewire::component('mailcoach::postmark-throttling-step', ThrottlingStepComponent::class);
        Livewire::component('mailcoach::postmark-feedback-step', FeedbackStepComponent::class);
        Livewire::component('mailcoach::postmark-summary-step', SummaryStepComponent::class);
    }
}
