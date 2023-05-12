<?php

namespace Spatie\Mailcoach\Http\App\ViewComposers;

use Illuminate\View\View;
use Spatie\QueryString\QueryString;

class QueryStringComposer
{
    protected QueryString $queryString;

    public function __construct(QueryString $queryString)
    {
        $this->queryString = $queryString;
    }

    public function compose(View $view)
    {
        $view->with('queryString', $this->queryString);
    }
}
