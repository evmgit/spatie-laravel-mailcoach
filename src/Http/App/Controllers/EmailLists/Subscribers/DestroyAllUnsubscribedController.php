<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class DestroyAllUnsubscribedController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        $emailList->allSubscribersWithoutIndex()->unsubscribed()->delete();

        flash()->success(__('All unsubscribers of the list have been deleted.'));

        return back();
    }
}
