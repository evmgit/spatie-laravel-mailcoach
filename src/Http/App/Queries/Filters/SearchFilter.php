<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\Filters\Filter;

class SearchFilter implements Filter
{
    use UsesMailcoachModels;

    /** @var string[] */
    protected array $fields;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if ($query->from === self::getSubscriberTableName() && config('mailcoach.encryption.enabled')) {
            if (str_contains($value, '@')) {
                $query->orWhere(function (Builder $builder) use ($value, $query) {
                    $builder->whereBlind('email', 'email_first_part', $value);
                    $builder->whereBlind('email', 'email_second_part', $value);

                    $query->getQuery()->joins = $builder->getQuery()->joins;
                });
            } else {
                $query->orWhereBlind('email', 'email_first_part', Str::finish($value, '@'));
                $query->orWhereBlind('email', 'email_second_part', Str::start($value, '@'));
            }

            $query->orWhereBlind('first_name', 'first_name', $value);
            $query->orWhereBlind('last_name', 'last_name', $value);

            return $query;
        }

        $query->search($value);
        $query->distinct();

        return $query;
    }
}
