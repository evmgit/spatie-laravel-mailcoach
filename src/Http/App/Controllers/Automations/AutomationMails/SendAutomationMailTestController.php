<?php


namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Requests\Automation\Mail\SendAutomationMailTestRequest;

class SendAutomationMailTestController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail, SendAutomationMailTestRequest $request)
    {
        $this->authorize('view', $mail);

        cache()->put('mailcoach-test-email-addresses', $request->emails, (int)CarbonInterval::month()->totalSeconds);

        $mail->sendTestMail($request->sanitizedEmails());

        $this->flashSuccessMessage($request);

        return back();
    }

    protected function flashSuccessMessage(SendAutomationMailTestRequest $request): void
    {
        if (count($request->sanitizedEmails()) > 1) {
            $emailCount = count($request->sanitizedEmails());

            flash()->success(__('A test email was sent to :count addresses.', ['count' => $emailCount]));

            return;
        }

        flash()->success(__('A test email was sent to :email.', ['email' => $request->sanitizedEmails()[0]]));
    }
}
