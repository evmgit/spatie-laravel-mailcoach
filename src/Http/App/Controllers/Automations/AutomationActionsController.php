<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationActionsController
{
    use UsesMailcoachModels;

    public function index(Automation $automation)
    {
        return view('mailcoach::app.automations.actions', compact(
            'automation',
        ));
    }

    public function store(
        Automation $automation,
        Request $request,
    ) {
        $newActions = json_decode($request->get('actions'), associative: true);

        $automation->chain($newActions);

        flash()->success(__('Actions successfully saved to automation :automation.', [
            'automation' => $automation->name,
        ]));

        return redirect()->route('mailcoach.automations.actions', $automation);
    }
}
