<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SegmentsQuery extends QueryBuilder
{
    public function __construct(EmailList $emailList)
    {
        $query = $emailList->segments()->getQuery();

        parent::__construct($query);

        $this
            ->defaultSort('name')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            );
    }
}
