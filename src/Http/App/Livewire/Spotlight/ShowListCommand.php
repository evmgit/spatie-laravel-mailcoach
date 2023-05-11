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
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShowListCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Show :resource', ['resource' => 'email list']);
    }

    public function getSynonyms(): array
    {
        return [
            __mc('View :resource', ['resource' => 'email list']),
            __mc('Go :resource', ['resource' => 'email list']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('list')->setPlaceholder('Email list')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchList($query)
    {
        return self::getEmailListClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->withCount('subscribers')
            ->get()
            ->map(function (EmailList $list) {
                return new SpotlightSearchResult(
                    $list->id,
                    $list->name,
                    "{$list->subscribers_count}"
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getEmailListClass());
    }

    public function execute(Spotlight $spotlight, EmailList $list)
    {
        $spotlight->redirect(route('mailcoach.emailLists.summary', $list));
    }
}
