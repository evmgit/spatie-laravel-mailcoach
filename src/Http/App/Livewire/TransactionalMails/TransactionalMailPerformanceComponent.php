<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\MainNavigation;

class TransactionalMailPerformanceComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public TransactionalMailLogItem $transactionalMail;

    public function mount(TransactionalMailLogItem $transactionalMail)
    {
        $this->transactionalMail = $transactionalMail;

        app(MainNavigation::class)->activeSection()?->add($this->transactionalMail->subject, route('mailcoach.transactionalMails'));
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.performance')
            ->layout('mailcoach::app.transactionalMails.layouts.transactional', [
                'title' => __mc('Performance'),
                'transactionalMail' => $this->transactionalMail,
            ]);
    }
}
