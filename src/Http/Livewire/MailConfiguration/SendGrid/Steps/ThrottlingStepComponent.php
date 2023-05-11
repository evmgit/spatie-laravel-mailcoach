<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps;

use Spatie\Mailcoach\Http\Livewire\MailConfiguration\AbstractThrottlingStepComponent;

class ThrottlingStepComponent extends AbstractThrottlingStepComponent
{
    public int $timespanInSeconds = 1;

    public int $mailsPerTimeSpan = 100;

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.sendGrid.throttling');
    }
}
