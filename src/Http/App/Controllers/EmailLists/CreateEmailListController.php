<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Actions\EmailLists\UpdateEmailListAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListGeneralSettingsRequest;

class CreateEmailListController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(UpdateEmailListGeneralSettingsRequest $request, UpdateEmailListAction $updateEmailListAction)
    {
        $this->authorize('create', static::getEmailListClass());

        $emailListClass = $this->getEmailListClass();

        $emailList = new $emailListClass;

        $updateEmailListAction->execute($emailList, $request);

        flash()->success(__('List :emailList was created', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.general-settings', $emailList);
    }
}
