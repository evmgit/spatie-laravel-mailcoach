<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UsersQuery extends QueryBuilder
{
    public function __construct(?Request $request = null)
    {
        parent::__construct(User::query(), $request);

        $this
            ->defaultSort('email')
            ->allowedSorts('email', 'name')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('email', 'name'))
            );
    }
}
