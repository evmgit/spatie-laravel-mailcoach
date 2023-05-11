<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\Filters\Filter;

class FuzzyFilter implements Filter
{
    use UsesMailcoachModels;

    /** @var string[] */
    protected array $fields;

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    public function __invoke(Builder $query, $values, string $property): Builder
    {
        $values = Arr::wrap($values);

        $query->where(function (Builder $builder) use ($values, $query) {
            $this
                ->addDirectFields($builder, $values)
                ->addRelationShipFields($builder, $values);

            $query->getQuery()->joins = array_merge(
                $query->getQuery()->joins ?? [],
                $builder->getQuery()->joins ?? []
            );
        });

        return $query;
    }

    public function addDirectFields(Builder $query, $values): FuzzyFilter
    {
        collect($this->fields)
            ->reject(fn (string $field) => Str::contains($field, '.'))
            ->each(function (string $field) use ($query, $values) {
                foreach ($values as $value) {
                    $query->orWhere($field, 'LIKE', "%{$value}%");
                }
            });

        return $this;
    }

    public function addRelationShipFields(Builder $query, $values): FuzzyFilter
    {
        collect($this->fields)
            ->filter(fn (string $field) => Str::contains($field, '.'))
            ->each(function (string $field) use ($query, $values) {
                [$relation, $field] = explode('.', $field);

                foreach ($values as $value) {
                    $query->orWhereHas($relation, function (Builder $query) use ($field, $value) {
                        $value = str_replace('+', ' ', $value);
                        $query->where($field, 'LIKE', "%{$value}%");
                    });
                }
            });

        return $this;
    }
}
