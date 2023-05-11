<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignProblemsQuery extends QueryBuilder
{
    public function __construct(Campaign $campaign)
    {
        parent::__construct(SendFeedbackItem::query()
            ->whereHas('send', function (Builder $query) use ($campaign) {
                $query->where('campaign_id', $campaign->id);
            }));

        $this
            ->with(['send.subscriber'])
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(['created_at', 'id'])
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'subscriber.email',
                    )
                )
            );
    }
}
