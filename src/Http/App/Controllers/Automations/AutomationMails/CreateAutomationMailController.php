<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Automation\Mail\StoredAutomationMailRequest;

class CreateAutomationMailController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(
        StoredAutomationMailRequest $request,
        UpdateAutomationMailAction $updateAutomationMailAction
    ) {
        $automationMailClass = static::getAutomationMailClass();

        $this->authorize('create', $automationMailClass);

        $automationMail = new $automationMailClass;

        $automationMail = $updateAutomationMailAction->execute(
            $automationMail,
            $request->validated(),
        );

        flash()->success(__('Email :name was created.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }
}
