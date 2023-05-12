<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Automation\RunAutomationRequest;

class RunAutomationController
{
    use UsesMailcoachModels;

    public function edit(Automation $automation)
    {
        return view('mailcoach::app.automations.run', [
            'automation' => $automation,
        ]);
    }

    public function update(
        Automation $automation,
        RunAutomationRequest $request
    ) {
        $automation->update([
            'interval' => $request->get('interval'),
        ]);

        flash()->success(__('Automation :automation was updated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.run', $automation);
    }
}
