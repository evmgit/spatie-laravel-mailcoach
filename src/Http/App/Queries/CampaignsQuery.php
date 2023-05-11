<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
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

    public function __construct(?Request $request = null)
    {
        parent::__construct(self::getCampaignClass()::query()->with('emailList')->withCount('sendsWithErrors'), $request);

        $sentSort = AllowedSort::custom('sent', (new CampaignSort()))->defaultDirection('desc');

        $filterFields = array_map('trim', config('mailcoach.campaigns.search_fields', ['name']));

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
                AllowedFilter::custom('search', new FuzzyFilter(...$filterFields)),
                AllowedFilter::custom('status', new CampaignStatusFilter()),
                AllowedFilter::exact('email_list_id'),
            );
    }
}
