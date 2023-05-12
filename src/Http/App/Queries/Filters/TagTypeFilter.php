<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\QueryBuilder\Filters\Filter;

class TagTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (in_array($value, [TagType::DEFAULT, TagType::MAILCOACH])) {
            return $query->where('type', $value);
        }

        return $query;
    }
}
