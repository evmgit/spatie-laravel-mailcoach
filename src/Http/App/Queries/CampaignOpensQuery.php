<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignOpensQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public int $totalCount;

    public function __construct(Campaign $campaign)
    {
        $prefix = DB::getTablePrefix();

        $campaignOpenTable = static::getCampaignOpenTableName();

        $query = static::getCampaignOpenClass()::query()
            ->selectRaw("
                {$prefix}{$campaignOpenTable}.subscriber_id as subscriber_id,
                {$prefix}{$this->getSubscriberTableName()}.email_list_id as subscriber_email_list_id,
                {$prefix}{$this->getSubscriberTableName()}.email as subscriber_email,
                count({$prefix}{$campaignOpenTable}.subscriber_id) as open_count,
                min({$prefix}{$campaignOpenTable}.created_at) AS first_opened_at
            ")
            ->join(static::getCampaignTableName(), static::getCampaignTableName().'.id', '=', "{$campaignOpenTable}.campaign_id")
            ->join($this->getSubscriberTableName(), "{$this->getSubscriberTableName()}.id", '=', "{$campaignOpenTable}.subscriber_id")
            ->where(static::getCampaignTableName().'.id', $campaign->id);

        $this->totalCount = $query->count();

        parent::__construct($query);

        $this
            ->defaultSort('-first_opened_at')
            ->allowedSorts('email', 'open_count', 'first_opened_at')
            ->groupBy("{$campaignOpenTable}.subscriber_id", "{$this->getSubscriberTableName()}.email_list_id", "{$this->getSubscriberTableName()}.email")
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'email'
                    )
                )
            );
    }
}
