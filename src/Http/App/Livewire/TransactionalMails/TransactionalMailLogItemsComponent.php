<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailQuery;

class TransactionalMailLogItemsComponent extends DataTableComponent
{
    public string $sort = '-created_at';

    public function deleteTransactionalMail(int $id)
    {
        $transactionalMail = self::getTransactionalMailLogItemClass()::find($id);

        $this->authorize('delete', $transactionalMail);

        $transactionalMail->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __mc('The mail was removed from the log'),
        ]);
    }

    public function getTitle(): string
    {
        return __mc('Log');
    }

    public function getView(): string
    {
        return 'mailcoach::app.transactionalMails.index';
    }

    public function getData(Request $request): array
    {
        $this->authorize('viewAny', static::getTransactionalMailLogItemClass());

        return [
            'transactionalMails' => (new TransactionalMailQuery($request))->paginate($request->per_page),
            'transactionalMailsCount' => self::getTransactionalMailLogItemClass()::count(),
        ];
    }
}
