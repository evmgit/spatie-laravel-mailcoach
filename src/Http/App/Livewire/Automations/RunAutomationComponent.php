<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class RunAutomationComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public Automation $automation;

    public string $error;

    protected function rules(): array
    {
        return [
            'automation.interval' => ['required'],
        ];
    }

    public function mount(Automation $automation)
    {
        $this->automation = $automation;
        $this->automation->interval ??= '10 minutes';

        $this->authorize('update', $this->automation);

        app(MainNavigation::class)->activeSection()?->add($this->automation->name, route('mailcoach.automations'));
    }

    public function pause(): void
    {
        $this->automation->pause();
        $this->dispatchBrowserEvent('automation-paused');
    }

    public function start(): void
    {
        try {
            $this->automation->start();
            $this->dispatchBrowserEvent('automation-started');
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function save()
    {
        $this->validate();

        $this->automation->save();

        $this->flash(__mc('Automation :automation was updated.', ['automation' => $this->automation->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.run')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => __mc('Run'),
            ]);
    }
}
