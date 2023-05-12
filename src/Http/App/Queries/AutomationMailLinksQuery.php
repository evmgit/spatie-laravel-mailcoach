<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailLinksQuery extends QueryBuilder
{
    public function __construct(AutomationMail $mail)
    {
        $query = $mail
            ->links()
            ->getQuery();

        parent::__construct($query);

        $this
            ->defaultSort('-unique_click_count')
            ->allowedSorts('unique_click_count', 'click_count')
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'url'
                    )
                )
            );
    }
}
