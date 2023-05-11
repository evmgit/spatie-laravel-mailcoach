<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailDeliveryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('view', $automationMail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.mails.delivery', [
            'links' => $this->mail->htmlLinks(),
        ])->layout('mailcoach::app.automations.mails.layouts.automationMail', [
            'title' => __mc('Delivery'),
            'mail' => $this->mail,
        ]);
    }
}
