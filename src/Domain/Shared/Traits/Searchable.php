<?php

namespace Spatie\Mailcoach\Domain\Shared\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Trait Searchable
 * Adapted & simplified from Searchable Trait package
 *
 * @see https://github.com/nicolaslopezj/searchable
 *
 * @property string $table
 * @property string $primaryKey
 *
 * @method string getTable()
 */
trait Searchable
{
    protected array $search_bindings = [];

    protected array $searchable = [];

    protected function getSearchableConfig(): array
    {
        return $this->searchable;
    }

    public function scopeSearch(Builder $q, string $search, float|int $threshold = null, bool $entireText = false, bool $entireTextOnly = false): Builder
    {
        return $this->scopeSearchRestricted($q, $search, null, $threshold, $entireText, $entireTextOnly);
    }

    public function scopeSearchRestricted(
        Builder $q,
        string $search,
        ?callable $restriction,
        float|int $threshold = null,
        bool $entireText = false,
        bool $entireTextOnly = false
    ): Builder {
        $query = clone $q;
        $query->withoutGlobalScopes();
        $query->select($this->getTable().'.*');
        $this->makeJoins($query);

        $search = mb_strtolower(trim($search));

        /* Splits the string on whitespace, except when quoted */
        preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/', $search, $matches);
        $words = $matches[1];
        $matchCount = count($matches);
        for ($i = 2; $i < $matchCount; $i++) {
            $words = array_filter($words) + $matches[$i];
        }

        $selects = [];
        $this->search_bindings = [];
        $relevance_count = 0;

        foreach ($this->getColumns() as $column => $relevance) {
            $relevance_count += $relevance;

            if (! $entireTextOnly) {
                $queries = $this->getSearchQueriesForColumn($column, $relevance, $words);
            } else {
                $queries = [];
            }

            if (($entireText === true && count($words) > 1) || $entireTextOnly === true) {
                $queries[] = $this->getSearchQuery($column, $relevance, [$search], 50, '', '');
                $queries[] = $this->getSearchQuery($column, $relevance, [$search], 30, '%', '%');
            }

            foreach ($queries as $select) {
                if (! empty($select)) {
                    $selects[] = $select;
                }
            }
        }

        $this->addSelectsToQuery($query, $selects);

        // Default the threshold if no value was passed.
        if (is_null($threshold)) {
            $threshold = $relevance_count / count($this->getColumns());
        }

        if (! empty($selects)) {
            $this->filterQueryWithRelevance($query, $selects, $threshold);
        }

        $this->makeGroupBy($query);

        if (is_callable($restriction)) {
            $query = $restriction($query);
        }

        $this->mergeQueries($query, $q);

        return $q;
    }

    protected function getDatabaseDriver(): string
    {
        $key = $this->connection ?: Config::get('database.default');

        return strtolower(Config::get('database.connections.'.$key.'.driver'));
    }

    protected function getColumns(): array
    {
        if (array_key_exists('columns', $this->getSearchableConfig())) {
            $driver = $this->getDatabaseDriver();
            $prefix = Config::get("database.connections.$driver.prefix");
            $columns = [];
            foreach ($this->getSearchableConfig()['columns'] as $column => $priority) {
                $columns[$prefix.$column] = $priority;
            }

            return $columns;
        }

        return DB::connection()->getSchemaBuilder()->getColumnListing($this->table);
    }

    protected function getGroupBy(): array|string|false
    {
        if (array_key_exists('groupBy', $this->getSearchableConfig())) {
            return $this->getSearchableConfig()['groupBy'];
        }

        return false;
    }

    protected function getJoins(): array
    {
        return Arr::get($this->getSearchableConfig(), 'joins', []);
    }

    protected function makeJoins(Builder $query): void
    {
        foreach ($this->getJoins() as $table => $keys) {
            $query->leftJoin($table, function ($join) use ($keys) {
                $join->on($keys[0], '=', $keys[1]);
                if (array_key_exists(2, $keys) && array_key_exists(3, $keys)) {
                    $join->whereRaw($keys[2].' = "'.$keys[3].'"');
                }
            });
        }
    }

    protected function makeGroupBy(Builder $query): void
    {
        if ($groupBy = $this->getGroupBy()) {
            $query->groupBy($groupBy);
        } else {
            $columns = $this->getTable().'.'.$this->primaryKey;

            $query->groupBy($columns);

            $joins = array_keys(($this->getJoins()));

            foreach ($this->getColumns() as $column => $relevance) {
                array_map(function ($join) use ($column, $query) {
                    if (str_contains($column, $join)) {
                        $query->groupBy($column);
                    }
                }, $joins);
            }
        }
    }

    protected function addSelectsToQuery(Builder $query, array $selects): void
    {
        if (! empty($selects)) {
            $query->selectRaw('max('.implode(' + ', $selects).') as '.'relevance',
                $this->search_bindings);
        }
    }

    protected function filterQueryWithRelevance(Builder $query, array $selects, float|int $relevance_count): void
    {
        $comparator = $this->isMysqlDatabase() ? 'relevance' : implode(' + ', $selects);

        $formattedRelevance = number_format($relevance_count, 2, '.', '');

        $bindings = $this->isMysqlDatabase()
            ? []
            : $this->search_bindings;

        $query->havingRaw("$comparator >= $formattedRelevance", $bindings);
        $query->orderBy('relevance', 'desc');
    }

    protected function getSearchQueriesForColumn(string $column, float|int $relevance, array $words): array
    {
        return [
            $this->getSearchQuery($column, $relevance, $words, 15),
            $this->getSearchQuery($column, $relevance, $words, 5, '', '%'),
            $this->getSearchQuery($column, $relevance, $words, 1, '%', '%'),
        ];
    }

    protected function getSearchQuery(
        string $column,
        float|int $relevance,
        array $words,
        float|int $relevance_multiplier,
        string $pre_word = '',
        string $post_word = ''
    ): string {
        $like_comparator = $this->isPostgresqlDatabase() ? 'ILIKE' : 'LIKE';
        $cases = [];

        foreach ($words as $word) {
            $cases[] = $this->getCaseCompare($column, $like_comparator, $relevance * $relevance_multiplier);
            $this->search_bindings[] = $pre_word.$word.$post_word;
        }

        return implode(' + ', $cases);
    }

    private function isMysqlDatabase(): bool
    {
        return $this->getDatabaseDriver() === 'mysql';
    }

    private function isPostgresqlDatabase(): bool
    {
        return $this->getDatabaseDriver() === 'pgsql';
    }

    protected function getCaseCompare(string $column, string $compare, float|int $relevance): string
    {
        if ($this->isPostgresqlDatabase()) {
            $field = 'LOWER('.$column.') '.$compare.' ?';

            return '(case when '.$field.' then '.$relevance.' else 0 end)';
        }

        $column = str_replace('.', '`.`', $column);
        $field = 'LOWER(`'.$column.'`) '.$compare.' ?';

        return '(case when '.$field.' then '.$relevance.' else 0 end)';
    }

    protected function mergeQueries(Builder $clone, Builder $original): void
    {
        $tableName = DB::connection($this->connection)->getTablePrefix().$this->getTable();
        if ($this->isPostgresqlDatabase()) {
            $original->from(DB::connection($this->connection)->raw("({$clone->toSql()}) as {$tableName}"));
        } else {
            $original->from(DB::connection($this->connection)->raw("({$clone->toSql()}) as `{$tableName}`"));
        }

        // First create a new array merging bindings
        $mergedBindings = array_merge_recursive(
            $clone->getBindings(),
            $original->getBindings(),
        );

        // Then apply bindings WITHOUT global scopes which are already included. If not, there is a strange behaviour
        // with some scope's bindings remaning
        $original->withoutGlobalScopes()->setBindings($mergedBindings);
    }
}
