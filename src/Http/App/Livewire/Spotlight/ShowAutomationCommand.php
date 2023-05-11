<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShowAutomationCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Show :resource', ['resource' => 'automation']);
    }

    public function getSynonyms(): array
    {
        return [
            __mc('View :resource', ['resource' => 'automation']),
            __mc('Go :resource', ['resource' => 'automation']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('automation')->setPlaceholder('Automation')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchAutomation($query)
    {
        return self::getAutomationClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->get()
            ->map(function (Automation $automation) {
                return new SpotlightSearchResult(
                    $automation->id,
                    $automation->name,
                    null,
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getAutomationClass());
    }

    public function execute(Spotlight $spotlight, Automation $automation)
    {
        $spotlight->redirect(route('mailcoach.automations.settings', $automation));
    }
}
