<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps;

use Spatie\Mailcoach\Http\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 36;

    public int $mailsPerTimeSpan = 1;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.mailgun.throttling');
    }
}
