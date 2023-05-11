<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Create automation');
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('name')->setPlaceholder('Automation name')->setType(SpotlightCommandDependency::INPUT))
            ->add(SpotlightCommandDependency::make('emailList')->setPlaceholder('Email list')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchEmailList($query)
    {
        return self::getEmailListClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->get()
            ->map(function (EmailList $list) {
                return new SpotlightSearchResult(
                    $list->id,
                    $list->name,
                    sprintf('Create automation for %s', $list->name)
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('create', self::getAutomationClass());
    }

    public function execute(Spotlight $spotlight, string $name, ?EmailList $emailList)
    {
        if (! $name || ! $emailList) {
            $spotlight->dispatchBrowserEvent('notify', [
                'content' => 'Name & email list is required',
                'type' => 'error',
            ]);

            return;
        }

        $automation = resolve(CreateAutomationAction::class)->execute([
            'name' => $name,
            'email_list_id' => $emailList->id,
        ]);

        flash()->success(__mc('Automation :automation was created.', ['automation' => $automation->name]));

        $spotlight->redirect(route('mailcoach.automations.settings', $automation));
    }
}
