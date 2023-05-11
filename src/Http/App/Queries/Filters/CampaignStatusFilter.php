<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CampaignStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($value === 'sent') {
            return $query->sendingOrSent();
        }

        if ($value === 'scheduled') {
            return $query->scheduled();
        }

        if ($value === 'draft') {
            return $query->draft();
        }

        return $query;
    }
}
