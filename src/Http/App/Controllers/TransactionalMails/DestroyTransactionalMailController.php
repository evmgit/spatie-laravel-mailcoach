<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class DestroyTransactionalMailController
{
    use AuthorizesRequests;

    public function __invoke(TransactionalMail $transactionalMail)
    {
        $this->authorize('delete', $transactionalMail);

        $transactionalMail->delete();

        flash()->success(__('The mail was removed from the log'));

        return redirect()->route('mailcoach.transactionalMails');
    }
}
