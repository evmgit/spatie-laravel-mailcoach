<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionalMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct($this->getTransactionalMailClass()::query());

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(
                'subject',
                'created_at',
                'id',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('subject')),
            );
    }
}
