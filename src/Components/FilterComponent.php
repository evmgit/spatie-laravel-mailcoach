<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\QueryString\QueryString;

class FilterComponent extends Component
{
    private QueryString $queryString;

    private string $attribute;

    private string $activeOn;

    public function __construct(QueryString $queryString, string $attribute, string $activeOn)
    {
        $this->queryString = $queryString;

        $this->attribute = $attribute;

        $this->activeOn = $activeOn;
    }

    public function href(): string
    {
        return $this->activeOn === ""
            ? $this->queryString->disable("filter[{$this->attribute}]")
            : $this->queryString->enable("filter[{$this->attribute}]", $this->activeOn);
    }

    public function active(): bool
    {
        return $this->activeOn === ""
            ? ! $this->queryString->isActive("filter[{$this->attribute}]")
            : $this->queryString->isActive("filter[{$this->attribute}]", $this->activeOn);
    }

    public function render()
    {
        return view('mailcoach::app.components.filters.filter');
    }
}
