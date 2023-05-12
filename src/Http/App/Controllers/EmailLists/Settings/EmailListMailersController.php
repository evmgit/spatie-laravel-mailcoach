<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListMailersRequest;

class EmailListMailersController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.settings.mailers', [
            'emailList' => $emailList,
        ]);
    }

    public function update(EmailList $emailList, UpdateEmailListMailersRequest $request)
    {
        $this->authorize('update', $emailList);

        $emailList->update([

            'campaign_mailer' => $request->campaign_mailer,
            'transactional_mailer' => $request->transactional_mailer,
        ]);

        flash()->success(__('List :emailList was updated', ['emailList' => $emailList->name]));

        return back();
    }
}
