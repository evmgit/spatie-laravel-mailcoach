<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Spatie\QueryString\QueryString;

class THComponent extends Component
{
    public bool $sortable;

    private ?string $sortBy;

    private bool $sortDefault;

    private bool $sortDefaultDesc;

    private string $sortField;

    private QueryString $queryString;

    public function __construct(QueryString $queryString, string $sortBy = null, bool $sortDefault = false)
    {
        $this->sortable = ! is_null($sortBy);

        $this->sortBy = $sortBy;

        $this->sortDefault = $sortDefault;
        $this->sortDefaultDesc = Str::startsWith($sortBy, '-');

        $this->sortField = ltrim($sortBy, '-');

        $this->queryString = $queryString;
    }

    public function href(): QueryString
    {
        if (! $this->sortable) {
            return $this->queryString;
        }

        if (! $this->sortDefault) {
            return $this->queryString->sort($this->sortBy);
        }

        if ($this->sortDefaultDesc) {
            return $this->queryString->isActive('sort')
                ? $this->queryString->disable('sort')
                : $this->queryString->enable('sort', $this->sortField);
        }

        return $this->queryString->isActive('sort')
            ? $this->queryString->disable('sort')
            : $this->queryString->enable('sort', '-' . $this->sortField);
    }

    public function isSortedAsc(): bool
    {
        if ($this->sortDefault && ! $this->queryString->isActive('sort') && ! $this->sortDefaultDesc) {
            return true;
        }

        return $this->queryString->isActive('sort', $this->sortField);
    }

    public function isSortedDesc(): bool
    {
        if ($this->sortDefault && ! $this->queryString->isActive('sort') && $this->sortDefaultDesc) {
            return true;
        }

        return $this->queryString->isActive('sort', '-' . $this->sortField);
    }

    public function render()
    {
        return view('mailcoach::app.components.table.th');
    }
}
