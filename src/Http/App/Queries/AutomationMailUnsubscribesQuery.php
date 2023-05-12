<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailUnsubscribesQuery extends QueryBuilder
{
    public function __construct(AutomationMail $automationMail)
    {
        parent::__construct($automationMail->unsubscribes()->getQuery());

        $this
            ->defaultSort('-created_at')
            ->allowedSorts('created_at')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(
                    'subscriber.email',
                    'subscriber.first_name',
                    'subscriber.last_name'
                ))
            );
    }
}
