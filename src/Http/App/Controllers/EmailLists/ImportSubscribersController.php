<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\ImportSubscribersRequest;

class ImportSubscribersController
{
    use AuthorizesRequests;

    public function showImportScreen(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.subscribers.import', [
            'emailList' => $emailList,
            'subscriberImports' => $emailList->subscriberImports()->latest()->get(),
        ]);
    }

    public function import(EmailList $emailList, ImportSubscribersRequest $request)
    {
        $this->authorize('update', $emailList);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport $subscriberImport */
        $subscriberImport = SubscriberImport::create([
            'email_list_id' => $emailList->id,
            'subscribe_unsubscribed' => $request->subscribeUnsubscribed(),
            'unsubscribe_others' => $request->unsubscribeMissing(),
            'replace_tags' => $request->replaceTags(),
        ]);

        $this->addMediaToSubscriberImport($request, $subscriberImport);

        $user = auth()->user();

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null));

        flash()->success(__('Your file has been uploaded. Follow the import status in the list below.'));

        return redirect()->back();
    }

    protected function addMediaToSubscriberImport(
        ImportSubscribersRequest $request,
        SubscriberImport $subscriberImport
    ): void {
        if ($request->has('file')) {
            $subscriberImport
                ->addMediaFromRequest('file')
                ->toMediaCollection('importFile');

            return;
        }

        $subscriberImport
            ->addMediaFromString($request->subscribers_csv)
            ->usingFileName('subscribers.csv')
            ->toMediaCollection('importFile');
    }
}
