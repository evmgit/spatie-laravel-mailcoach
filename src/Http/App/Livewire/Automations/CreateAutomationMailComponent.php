<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationMailComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $name = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
        ];
    }

    public function saveAutomationMail()
    {
        $automationMailClass = self::getAutomationMailClass();

        $this->authorize('create', $automationMailClass);

        $automationMail = resolve(UpdateAutomationMailAction::class)->execute(
            new $automationMailClass,
            $this->validate(),
        );

        flash()->success(__mc('Email :name was created.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }

    public function render()
    {
        return view('mailcoach::app.automations.mails.partials.create');
    }
}
