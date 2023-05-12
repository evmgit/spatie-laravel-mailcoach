<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Requests\TransactionalMails\SendTransactionalMailTestMailRequest;

class SendTransactionalMailTestController
{
    use AuthorizesRequests;

    public function __invoke(TransactionalMailTemplate $template, SendTransactionalMailTestMailRequest $request)
    {
        $this->authorize('view', $template);

        $mailable = $template->getMailable();

        cache()->put('mailcoach-test-transactional-template-email-addresses', $request->emails, (int)CarbonInterval::month()->totalSeconds);

        Mail::to($request->sanitizedEmails())->send($mailable);

        $this->flashSuccessMessage($request);

        return back();
    }

    protected function flashSuccessMessage(SendTransactionalMailTestMailRequest $request): void
    {
        if (count($request->sanitizedEmails()) > 1) {
            $emailCount = count($request->sanitizedEmails());

            flash()->success(__('A test email was sent to :count addresses.', ['count' => $emailCount]));

            return;
        }

        flash()->success(__('A test email was sent to :email.', ['email' => $request->sanitizedEmails()[0]]));
    }
}
