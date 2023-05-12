<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;

class EmailListsIndexController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(EmailListQuery $emailListQuery)
    {
        $this->authorize('viewAny', static::getEmailListClass());

        return view('mailcoach::app.emailLists.index', [
            'emailLists' => $emailListQuery->paginate(),
            'totalEmailListsCount' => static::getEmailListClass()::count(),
        ]);
    }
}
