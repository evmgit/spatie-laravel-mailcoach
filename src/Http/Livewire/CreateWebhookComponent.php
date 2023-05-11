<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateWebhookComponent extends Component
{
    use UsesMailcoachModels;

    public string $name = '';

    public string $url = '';

    public function saveWebhook()
    {
        $this->validate([
            'name' => ['required', 'string'],
            'url' => ['required', 'string', 'url', 'starts_with:https://'],
        ]);

        $mailer = self::getWebhookConfigurationClass()::create([
            'name' => $this->name,
            'url' => $this->url,
            'secret' => Str::random(),
        ]);

        flash()->success(__mc('The webhook has been created.'));

        return redirect()->route('webhooks.edit', $mailer);
    }

    public function render()
    {
        return view('mailcoach::app.configuration.webhooks.partials.create');
    }
}
