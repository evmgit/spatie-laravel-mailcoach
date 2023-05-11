<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class TransactionalMailResendComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;
    use LivewireFlash;

    public TransactionalMailLogItem $transactionalMail;

    public function mount(TransactionalMailLogItem $transactionalMail)
    {
        $this->transactionalMail = $transactionalMail;

        app(MainNavigation::class)->activeSection()?->add($this->transactionalMail->subject, route('mailcoach.transactionalMails'));
    }

    public function resend()
    {
        $this->transactionalMail->resend();

        $this->flash(__mc('The mail has been resent!'));
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.resend')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __mc('Resend'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
