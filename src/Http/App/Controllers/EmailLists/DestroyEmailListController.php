<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class DestroyEmailListController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('delete', $emailList);

        $emailList->delete();

        flash()->success(__('List :emailList was deleted', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists');
    }
}
