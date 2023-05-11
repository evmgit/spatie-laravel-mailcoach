<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SentMailsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct($this->getSendClass()::query());

        $this
            ->whereNotNull('sent_at')
            ->defaultSort('-sent_at')
            ->with(['campaign', 'subscription.subscriber'])
            ->allowedSorts(
                'created_at',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('campaign.name', 'subscription.subscriber.email'))
            );
    }
}
