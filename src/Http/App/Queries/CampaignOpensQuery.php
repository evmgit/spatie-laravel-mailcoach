<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
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

    public function __construct(Campaign $campaign, ?Request $request = null)
    {
        $prefix = DB::getTablePrefix();

        $campaignOpenTable = static::getCampaignOpenTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();

        $query = static::getCampaignOpenClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$campaignOpenTable}.subscriber_id) as open_count,
                min({$prefix}{$campaignOpenTable}.created_at) AS first_opened_at
            ")
            ->join(static::getCampaignTableName(), static::getCampaignTableName().'.id', '=', "{$campaignOpenTable}.campaign_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$campaignOpenTable}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->where(static::getCampaignTableName().'.id', $campaign->id);

        $this->totalCount = $query->count();

        parent::__construct($query, $request);

        $this
            ->defaultSort('-first_opened_at')
            ->allowedSorts('email', 'open_count', 'first_opened_at')
            ->groupBy('subscriber_uuid', 'subscriber_email_list_uuid', 'subscriber_email')
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
