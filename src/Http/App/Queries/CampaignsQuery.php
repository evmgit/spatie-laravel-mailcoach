<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\CampaignStatusFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Sorts\CampaignSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct($this->getCampaignClass()::query()->with('emailList'));

        $sentSort = AllowedSort::custom('sent', (new CampaignSort()))->defaultDirection('desc');

        $this
            ->defaultSort($sentSort)
            ->allowedSorts(
                'name',
                'email_list_id',
                'unique_open_count',
                'unique_click_count',
                'unsubscribe_rate',
                'sent_to_number_of_subscribers',
                $sentSort,
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name')),
                AllowedFilter::custom('status', new CampaignStatusFilter()),
                AllowedFilter::exact('email_list_id')
            );
    }
}
