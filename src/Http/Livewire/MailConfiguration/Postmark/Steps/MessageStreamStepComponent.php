<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\Steps;

use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns\UsesMailer;
use Spatie\MailcoachPostmarkSetup\MessageStream;
use Spatie\MailcoachPostmarkSetup\Postmark;

class MessageStreamStepComponent extends StepComponent
{
    use LivewireFlash;
    use UsesMailer;

    public string $streamId = '';

    public bool $streamsLoaded = false;

    public array $messageStreams = [];

    public $rules = [
        'streamId' => ['required'],
    ];

    public function mount()
    {
        $this->streamId = $this->mailer()->get('streamId', '');
    }

    public function submit()
    {
        $this->validate();

        $this->mailer()->merge([
            'streamId' => $this->streamId,
        ]);

        $this->nextStep();
    }

    public function loadStreams()
    {
        $postmark = (new Postmark($this->mailer()->get('apiKey')));
        $this->messageStreams = $postmark->getStreams()->mapWithKeys(fn (MessageStream $stream) => [$stream->id => $stream->name])->toArray();
        $this->streamsLoaded = true;
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Message Stream',
        ];
    }

    public function render()
    {
        return view('mailcoach::app.configuration.mailers.wizards.postmark.messageStream');
    }
}
