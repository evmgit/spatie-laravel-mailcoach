<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class PasswordComponent extends Component
{
    use LivewireFlash;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $rules = [
        'current_password' => ['required', 'current_password'],
        'password' => ['min:8', 'confirmed'],
    ];

    public function save()
    {
        $this->validate();

        Auth::user()->update(['password' => Hash::make($this->password)]);

        $this->flash(__mc('Your password has been updated.'));

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        return view('mailcoach::app.account.password')
            ->layout('mailcoach::app.layouts.settings', ['title' => __mc('Password')]);
    }
}
