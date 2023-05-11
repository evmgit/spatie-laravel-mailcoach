<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShowCampaignCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __mc('Show :resource', ['resource' => 'campaign']);
    }

    public function getSynonyms(): array
    {
        return [
            __mc('View :resource', ['resource' => 'campaign']),
            __mc('Go :resource', ['resource' => 'campaign']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('campaign')->setPlaceholder('Campaign')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchCampaign($query)
    {
        return self::getCampaignClass()::query()
            ->when($query, fn (Builder $builder) => $builder->where('name', 'like', "%$query%"))
            ->whereNotNull('name')
            ->limit(10)
            ->get()
            ->map(function (Campaign $campaign) {
                $emailList = $campaign->emailList?->name ?? '<deleted email list>';

                return new SpotlightSearchResult(
                    $campaign->id,
                    $campaign->name,
                    "{$emailList} - {$campaign->status}"
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getCampaignClass());
    }

    public function execute(Spotlight $spotlight, Campaign $campaign)
    {
        if ($campaign->isSent() || $campaign->isSending() || $campaign->isCancelled()) {
            $spotlight->redirect(route('mailcoach.campaigns.summary', $campaign));
        } elseif ($campaign->isScheduled()) {
            $spotlight->redirect(route('mailcoach.campaigns.delivery', $campaign));
        } else {
            $spotlight->redirect(route('mailcoach.campaigns.content', $campaign));
        }
    }
}
