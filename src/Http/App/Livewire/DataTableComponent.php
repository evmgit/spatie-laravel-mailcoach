<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\QueryBuilder;

abstract class DataTableComponent extends Component
{
    use LivewireFlash;
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use WithPagination;

    public bool $readyToLoad = false;

    public string $search = '';

    public string $sort = 'name';

    public int $perPage = 15;

    public array $selectedRows = [];

    public bool $selectedAll = false;

    public string $bulkAction = '';

    protected string $defaultSort;

    protected array $allowedFilters = [];

    abstract public function getTitle(): string;

    abstract public function getView(): string;

    abstract public function getData(Request $request): array;

    public function getQuery(Request $request): ?QueryBuilder
    {
        return null;
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.app';
    }

    public function getLayoutData(): array
    {
        return [];
    }

    public function boot()
    {
        $this->defaultSort = $this->sort;

        foreach ($this->allowedFilters as $filter => $options) {
            $this->$filter = $options['except'] ?? '';
        }
    }

    public function getQueryString()
    {
        return array_merge([
            'search' => ['except' => ''],
            'page' => ['except' => 1],
            'perPage' => ['except' => 15],
            'sort' => ['except' => $this->defaultSort],
        ], $this->allowedFilters);
    }

    public function sort(string $sort)
    {
        if ($this->sort === $sort && str_starts_with($sort, '-')) {
            return $this->sort = Str::replaceFirst('-', '', $this->sort);
        }

        if ($this->sort === $sort) {
            return $this->sort = '-'.$sort;
        }

        $this->sort = $sort;
    }

    public function setFilter(string $property, ?string $value = null)
    {
        $this->resetPage();

        if (is_null($value)) {
            $this->$property = null;

            return;
        }

        $this->$property = $value;
    }

    public function clearFilters()
    {
        $this->resetPage();
        $this->search = '';
        foreach ($this->allowedFilters as $filter => $options) {
            $this->$filter = $options['except'] ?? '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function isFiltering(): bool
    {
        return collect($this->allowedFilters)
            ->put('search', [])
            ->filter(function ($options, string $filter) {
                return $this->$filter !== ($options['except'] ?? '');
            })
            ->count() > 0;
    }

    public function loadRows()
    {
        $this->readyToLoad = true;
    }

    public function selectAll(bool $withoutPagination = false): void
    {
        $this->selectedRows = [];

        if ($this->selectedAll && ! $withoutPagination) {
            $this->selectedAll = false;

            return;
        }

        /** @var ?\Spatie\QueryBuilder\QueryBuilder $query */
        $query = $this->getQuery($this->buildRequest());

        if (! $query) {
            return;
        }

        $this->selectedAll = true;

        if ($withoutPagination) {
            $this->selectedRows = $query
                ->reorder('id')
                ->pluck('id')
                ->toArray();

            return;
        }

        $this->selectedRows = Arr::pluck($query->paginate()->items(), 'id');
    }

    public function select(string|int $row): void
    {
        $this->selectedAll = false;

        if (in_array($row, $this->selectedRows)) {
            array_splice($this->selectedRows, array_search($row, $this->selectedRows));

            return;
        }

        $this->selectedRows[] = $row;
    }

    public function resetSelect(): void
    {
        $this->selectedRows = [];
        $this->bulkAction = '';
        $this->selectedAll = false;
    }

    protected function buildRequest(): Request
    {
        $request = clone request();
        $request->query->set('sort', $this->sort);
        $request->query->set('per_page', $this->perPage);
        $request->query->set(
            'filter',
            collect($this->allowedFilters)
                ->keys()
                ->add('search')
                ->mapWithKeys(fn (string $filter) => [$filter => addcslashes($this->$filter, '%')])
                ->filter(fn ($value) => ! empty($value))
        );

        return $request;
    }

    public function render()
    {
        return view(
            $this->getView(),
            $this->readyToLoad ? $this->getData($this->buildRequest()) : []
        )->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
            'hideBreadcrumbs' => true,
        ], $this->getLayoutData()));
    }
}
