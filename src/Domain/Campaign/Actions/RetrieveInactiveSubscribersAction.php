<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotDetermineInactiveSubscribers;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\NotEnoughCampaigns;

class RetrieveInactiveSubscribersAction
{
    public function execute(
        EmailList $emailList,
        ?int $didNotOpenPastNumberOfCampaigns = null,
        ?int $didNotClickPastNumberOfCampaigns = null,
    ): HasMany {
        $query = $emailList->subscribers();

        if (is_null($didNotOpenPastNumberOfCampaigns) && is_null($didNotClickPastNumberOfCampaigns)) {
            throw CouldNotDetermineInactiveSubscribers::create($emailList);
        }

        if (! is_null($didNotOpenPastNumberOfCampaigns)) {
            $campaignIdsForOpens = $emailList->campaigns()->latest()->take($didNotOpenPastNumberOfCampaigns)->pluck('id');

            if ($campaignIdsForOpens->count() < $didNotOpenPastNumberOfCampaigns) {
                throw NotEnoughCampaigns::forOpens($didNotOpenPastNumberOfCampaigns);
            }

            $query
                ->whereHas('sends', fn (Builder $query) => $query->whereIn('campaign_id', $campaignIdsForOpens))
                ->whereDoesntHave('opens', fn (Builder $query) => $query->whereIn('campaign_id', $campaignIdsForOpens));
        }

        if (! is_null($didNotClickPastNumberOfCampaigns)) {
            $campaignIdsForClicks = $emailList->campaigns()->latest()->take($didNotClickPastNumberOfCampaigns)->pluck('id');

            if ($campaignIdsForClicks->count() < $didNotClickPastNumberOfCampaigns) {
                throw NotEnoughCampaigns::forClicks($didNotClickPastNumberOfCampaigns);
            }

            $query
                ->whereHas('sends', fn (Builder $query) => $query->whereIn('campaign_id', $campaignIdsForClicks))
                ->whereDoesntHave('clicks', fn (Builder $query) => $query->whereIn('campaign_id', $campaignIdsForClicks));
        }

        return $query;
    }
}
