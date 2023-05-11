<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\Steps;

use Illuminate\Support\Str;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns\UsesMailer;
use Spatie\MailcoachSendgridFeedback\SendgridWebhookController;
use Spatie\MailcoachSendgridSetup\EventType;
use Spatie\MailcoachSendgridSetup\Sendgrid;

class FeedbackStepComponent extends StepComponent
{
    use LivewireFlash;
    use UsesMailer;

    public bool $trackOpens = false;

    public bool $trackClicks = false;

    public array $rules = [
        'trackOpens' => ['boolean'],
        'trackClicks' => ['boolean'],
    ];

    public function mount()
    {
        $this->trackOpens = $this->mailer()->get('open_tracking_enabled', false);
        $this->trackClicks = $this->mailer()->get('click_tracking_enabled', false);
    }

    public function configureSendGrid()
    {
        $this->validate();

        $endpoint = action(SendgridWebhookController::class, $this->mailer()->configName());

        $events = [EventType::Bounce, EventType::Bounce];

        if ($this->trackOpens) {
            $events[] = EventType::Open;
        }

        if ($this->trackClicks) {
            $events[] = EventType::Click;
        }

        $secret = Str::random(20);

        $endpoint .= "?secret={$secret}";

        $this->getSendGrid()->setupWebhook($endpoint, $events);

        $this->mailer()->merge([
            'open_tracking_enabled' => $this->trackOpens,
            'click_tracking_enabled' => $this->trackClicks,
            'signing_secret' => $secret,
        ]);

        $this->mailer()->markAsReadyForUse();

        $this->flash('Your account has been configured to handle feedback.');

        $this->nextStep();
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.sendGrid.feedback');
    }

    protected function getSendGrid(): Sendgrid
    {
        return new Sendgrid($this->mailer()->get('apiKey'));
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Feedback',
        ];
    }
}
