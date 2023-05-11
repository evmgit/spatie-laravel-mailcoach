<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SegmentsQuery extends QueryBuilder
{
    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        $query = $emailList->segments()->getQuery();

        parent::__construct($query, $request);

        $this
            ->defaultSort('name')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            );
    }
}
