<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;

class SearchComponent extends Component
{
    public string $value;

    public string $placeholder;

    public function __construct(Request $request, string $placeholder = '')
    {
        $this->value = $request->query('filter', [])['search'] ?? '';
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('mailcoach::app.components.search');
    }
}
