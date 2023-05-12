<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Automation\AutomationSettingsRequest;

class AutomationSettingsController
{
    use UsesMailcoachModels;

    public function edit(Automation $automation)
    {
        return view('mailcoach::app.automations.settings', [
            'automation' => $automation,
        ]);
    }

    public function update(
        Automation $automation,
        AutomationSettingsRequest $request
    ) {
        $automation->update([
            'name' => $request->get('name'),
            'email_list_id' => $request->email_list_id,
            'segment_class' => $request->getSegmentClass(),
            'segment_id' => $request->segment_id,
        ]);

        $automation->triggerOn($request->trigger());

        $automation->update(['segment_description' => $automation->getSegment()->description()]);

        flash()->success(__('Automation :automation was updated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }
}
