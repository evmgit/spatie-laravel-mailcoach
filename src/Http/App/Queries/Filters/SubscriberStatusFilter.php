<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SubscriberStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'unconfirmed') {
            return $query->unconfirmed();
        }

        if ($value === 'subscribed') {
            return $query->subscribed();
        }

        if ($value === 'unsubscribed') {
            return $query->unsubscribed();
        }

        return $query;
    }
}
