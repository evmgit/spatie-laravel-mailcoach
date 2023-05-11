<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SendTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'pending') {
            return $query->pending();
        }

        if ($value === 'sent') {
            return $query->sent();
        }

        if ($value === 'failed') {
            return $query->failed();
        }

        if ($value === 'bounced') {
            return $query->bounced();
        }

        if ($value === 'complained') {
            return $query->complained();
        }

        return $query;
    }
}
