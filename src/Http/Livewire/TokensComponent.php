<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\PersonalAccessToken;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class TokensComponent extends Component
{
    use LivewireFlash;

    public string $name = '';

    public string $newToken = '';

    public array $rules = [
        'name' => 'required',
    ];

    public function save()
    {
        $this->validate();

        /** @var \Laravel\Sanctum\NewAccessToken $token */
        $token = Auth::user()->createToken($this->name);

        $this->newToken = $token->plainTextToken;

        $this->flash(__mc('The token has been created.'));

        $this->name = '';
    }

    public function delete(int $id)
    {
        $token = PersonalAccessToken::find($id);

        abort_unless($token->tokenable_id === Auth::id(), 403);

        $token->delete();

        $this->flash(__mc('The token has been deleted.'));
    }

    public function render()
    {
        return view('mailcoach::app.account.tokens', [
            'tokens' => Auth::user()->tokens ?? [],
        ])->layout('mailcoach::app.layouts.settings', ['title' => __mc('API Tokens')]);
    }
}
