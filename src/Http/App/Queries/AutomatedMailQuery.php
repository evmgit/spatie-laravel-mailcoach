<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomatedMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        parent::__construct($this::getAutomationMailClass()::query(), $request);

        $this
            ->defaultSort('name')
            ->allowedSorts(
                'name',
                'sent_to_number_of_subscribers',
                'unique_open_count',
                'unique_click_count',
                'created_at'
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name')),
            );
    }
}
