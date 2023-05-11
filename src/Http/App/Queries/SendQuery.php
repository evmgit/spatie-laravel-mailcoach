<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SendQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(Subscriber $subscriber, ?Request $request)
    {
        $query = self::getSendClass()::query()
            ->withCount(['opens', 'clicks'])
            ->with([
                'campaign',
                'automationMail',
                'transactionalMailLogItem',
            ])
            ->where('subscriber_id', $subscriber->id);

        parent::__construct($query, $request);

        $this
            ->defaultSort('-sent_at')
            ->allowedSorts('sent_at')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('campaign.name'))
            );
    }
}
