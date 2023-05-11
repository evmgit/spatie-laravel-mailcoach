<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Actions\EmailLists\UpdateEmailListAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\UpdateEmailListSettingsRequest;
use Spatie\Mailcoach\Http\Api\Resources\EmailListResource;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;

class EmailListsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(EmailListQuery $emailLists)
    {
        $this->authorize('viewAny', static::getEmailListClass());

        return EmailListResource::collection($emailLists->paginate());
    }

    public function show(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        return new EmailListResource($emailList);
    }

    public function store(UpdateEmailListSettingsRequest $request, UpdateEmailListAction $updateEmailListAction)
    {
        $emailListClass = static::getEmailListClass();

        $this->authorize('create', $emailListClass);

        $emailList = new $emailListClass;

        $emailList = $updateEmailListAction->execute($emailList, $request);

        return new EmailListResource($emailList);
    }

    public function update(UpdateEmailListSettingsRequest $request, EmailList $emailList, UpdateEmailListAction $updateEmailListAction)
    {
        $this->authorize('update', $emailList);

        $emailList = $updateEmailListAction->execute($emailList, $request);

        return new EmailListResource($emailList);
    }

    public function destroy(EmailList $emailList)
    {
        $this->authorize('delete', $emailList);

        $emailList->delete();

        return $this->respondOk();
    }
}
