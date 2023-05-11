<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\UsersQuery;

class UsersComponent extends DataTableComponent
{
    use LivewireFlash;

    public function getTitle(): string
    {
        return __mc('Users');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.users.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Users'),
        ];
    }

    public function deleteUser(int $id)
    {
        if ($id === Auth::user()->id) {
            $this->flashError(__mc('You cannot delete yourself!'));

            return;
        }

        $user = User::find($id);
        $user->delete();

        $this->flash(__mc('The user has been deleted.'));
    }

    public function getData(Request $request): array
    {
        return [
            'users' => (new UsersQuery($request))->paginate(),
            'totalUsersCount' => User::count(),
        ];
    }
}
