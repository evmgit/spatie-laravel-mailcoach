<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignUnsubscribesQuery extends QueryBuilder
{
    public function __construct(Campaign $campaign)
    {
        parent::__construct($campaign->unsubscribes()->getQuery());

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts('created_at', 'id')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(
                    'subscriber.email',
                    'subscriber.first_name',
                    'subscriber.last_name'
                ))
            );
    }
}
