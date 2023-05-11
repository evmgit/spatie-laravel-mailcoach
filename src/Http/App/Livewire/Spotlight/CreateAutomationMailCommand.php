<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationMailCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Create automation-mail');
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('name')->setPlaceholder('Mail name')->setType(SpotlightCommandDependency::INPUT));
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('create', self::getAutomationMailClass());
    }

    public function execute(Spotlight $spotlight, string $name)
    {
        if (! $name) {
            return;
        }

        $automationMailClass = self::getAutomationMailClass();

        $automationMail = resolve(UpdateAutomationMailAction::class)->execute(new $automationMailClass, [
            'name' => $name,
        ]);

        flash()->success(__mc('Email :name was created.', ['name' => $automationMail->name]));

        $spotlight->redirect(route('mailcoach.automations.mails.settings', $automationMail));
    }
}
