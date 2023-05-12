<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request, CreateAutomationAction $createAutomationAction)
    {
        $automation = $createAutomationAction->execute($request->validate(['name' => ['required']]));

        flash()->success(__('Automation :automation was created.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }
}
