<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\Steps;

use Exception;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns\UsesMailer;
use Spatie\MailcoachSendinblueSetup\Sendinblue;

class AuthenticationStepComponent extends StepComponent
{
    use LivewireFlash;
    use UsesMailer;

    public string $apiKey = '';

    public $rules = [
        'apiKey' => ['required'],
    ];

    public function mount()
    {
        $this->apiKey = $this->mailer()->get('apiKey', '');
    }

    public function submit()
    {
        $this->validate();

        try {
            $validApiKey = (new Sendinblue($this->apiKey))->isValidApiKey();
        } catch (Exception) {
            $this->flashError(__mc('Something went wrong communicating with Sendinblue.'));

            return;
        }

        if (! $validApiKey) {
            $this->addError('apiKey', __mc('This is not a valid API key.'));

            return;
        }

        $this->flash(__mc('The API key is correct.'));

        $this->mailer()->merge([
            'apiKey' => $this->apiKey,
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
        return view('mailcoach::app.configuration.mailers.wizards.sendinblue.authentication');
    }
}
