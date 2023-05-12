<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class DestroyAutomationMailController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $automationMail)
    {
        $this->authorize('delete', $automationMail);

        $automationMail->delete();

        flash()->success(__('Email :name was deleted.', ['name' => $automationMail->name]));

        return redirect()->back();
    }
}
