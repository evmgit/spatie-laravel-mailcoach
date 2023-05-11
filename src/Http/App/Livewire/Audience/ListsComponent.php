<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;

class ListsComponent extends DataTableComponent
{
    public function deleteList(int $id)
    {
        $list = self::getEmailListClass()::find($id);

        $this->authorize('delete', $list);

        self::getSubscriberClass()::where('email_list_id', $list->id)->delete();
        $list->delete();

        $this->flash(__mc('List :list was deleted.', ['list' => $list->name]));
    }

    public function getTitle(): string
    {
        return __mc('Lists');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.index';
    }

    public function getData(Request $request): array
    {
        $this->authorize('viewAny', static::getEmailListClass());

        return [
            'emailLists' => (new EmailListQuery($request))->paginate($request->per_page),
            'totalEmailListsCount' => static::getEmailListClass()::count(),
        ];
    }
}
