<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns\UsesMailer;
use Spatie\MailcoachMailgunSetup\Mailgun;

class AuthenticationStepComponent extends StepComponent
{
    use LivewireFlash;
    use UsesMailer;

    public string $apiKey = '';

    public string $domain = '';

    public string $baseUrl = '';

    public $rules = [
        'apiKey' => ['required'],
        'domain' => ['required'],
        'baseUrl' => ['required', 'in:api.mailgun.net,api.eu.mailgun.net'],
    ];

    public function mount()
    {
        $this->apiKey = $this->mailer()->get('apiKey', '');
        $this->domain = $this->mailer()->get('domain', '');
        $this->baseUrl = $this->mailer()->get('baseUrl', 'api.mailgun.net');
    }

    public function submit()
    {
        $this->validate();

        try {
            $validApiKey = (new Mailgun($this->apiKey, $this->domain, $this->baseUrl))->isValidApiKey();
        } catch (Exception) {
            $this->flash('Something went wrong communicating with Mailgun.', 'error');

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', __mc('These credentials are not valid.'));
            $this->addError('domain', __mc('These credentials are not valid.'));
            $this->addError('baseUrl', __mc('These credentials are not valid.'));

            return;
        }

        $this->flash('The credentials are correct.');

        $this->mailer()->merge([
            'apiKey' => $this->apiKey,
            'domain' => $this->domain,
            'baseUrl' => $this->baseUrl,
        ]);

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Authenticate',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.mailgun.authentication');
    }
}
