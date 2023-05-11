<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns\UsesMailer;

class SummaryStepComponent extends StepComponent
{
    use UsesMailer;

    public int $mailerId;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.postmark.summary', [
            'mailer' => $this->mailer(),
        ]);
    }

    public function sendTestEmail()
    {
    }

    public function startOver()
    {
        $this->showStep('mailcoach::ses-authentication-step');
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Summary',
        ];
    }
}
