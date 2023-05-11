<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShowTemplateCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Show :resource', ['resource' => 'template']);
    }

    public function getSynonyms(): array
    {
        return [
            __mc('View :resource', ['resource' => 'template']),
            __mc('Go :resource', ['resource' => 'template']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('template')->setPlaceholder('Template')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchTemplate($query)
    {
        return self::getTemplateClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->get()
            ->map(function (Template $template) {
                return new SpotlightSearchResult(
                    $template->id,
                    $template->name,
                    null,
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getTemplateClass());
    }

    public function execute(Spotlight $spotlight, Template $template)
    {
        $spotlight->redirect(route('mailcoach.templates.edit', $template));
    }
}
