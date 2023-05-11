<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct(
            $this->getSubscriberClass()::query()
                ->with('emailList')
        );

        $this
            ->defaultSort('-created_at', '-id')
            ->allowedSorts(['created_at', 'email', 'first_name', 'last_name', 'id'])
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('email', 'first_name', 'last_name')),
                AllowedFilter::exact('uuid'),
            );
    }
}
