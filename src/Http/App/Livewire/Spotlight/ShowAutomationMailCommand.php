<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShowAutomationMailCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Show :resource', ['resource' => 'automation mail']);
    }

    public function getSynonyms(): array
    {
        return [
            __mc('View :resource', ['resource' => 'automation mail']),
            __mc('Go :resource', ['resource' => 'automation mail']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('automationMail')->setPlaceholder('Automation mail')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchAutomationMail($query)
    {
        return self::getAutomationMailClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->get()
            ->map(function (AutomationMail $automationMail) {
                return new SpotlightSearchResult(
                    $automationMail->id,
                    $automationMail->name,
                    null,
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getAutomationMailClass());
    }

    public function execute(Spotlight $spotlight, AutomationMail $automationMail)
    {
        $spotlight->redirect(route('mailcoach.automations.mails.summary', $automationMail));
    }
}
