<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionalMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        parent::__construct($this->getTransactionalMailLogItemClass()::query(), $request);

        $filterFields = array_map('trim', config('mailcoach.transactional.search_fields', ['subject']));

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(
                'subject',
                'created_at',
                'id',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(...$filterFields)),
            );
    }
}
