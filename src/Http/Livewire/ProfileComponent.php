<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class ProfileComponent extends Component
{
    use LivewireFlash;

    public string $email;

    public string $name;

    public function rules()
    {
        return [
            'email' => ['required', 'email:rfc,dns', Rule::unique('users')->ignore(Auth::user()->id)],
            'name' => ['required'],
        ];
    }

    public function mount()
    {
        $this->email = Auth::user()->email;
        $this->name = Auth::user()->name;
    }

    public function save()
    {
        Auth::user()->update($this->validate());

        $this->flash(__mc('Your account has been updated.'));
    }

    public function render()
    {
        return view('mailcoach::app.account.index')
            ->layout('mailcoach::app.layouts.settings', ['title' => __mc('Profile')]);
    }
}
