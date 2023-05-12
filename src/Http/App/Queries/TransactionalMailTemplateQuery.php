<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionalMailTemplateQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct($this->getTransactionalMailTemplateClass()::query());

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(
                'name',
                'created_at',
                'id',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name', 'subject')),
            );
    }
}
