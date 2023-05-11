<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditUserComponent extends Component
{
    use LivewireFlash;

    public User $user;

    public function rules(): array
    {
        return [
            'user.email' => ['required', 'email:rfc', Rule::unique('users', 'email')->ignore($this->user->id)],
            'user.name' => ['required', 'string'],
        ];
    }

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function save()
    {
        $this->user->update($this->validate());

        $this->flash(__mc('The user has been updated.'));
    }

    public function render()
    {
        return view('mailcoach::app.configuration.users.edit')
            ->layout('mailcoach::app.layouts.settings', ['title' => $this->user->name]);
    }
}
