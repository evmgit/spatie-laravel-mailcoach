<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;
use Throwable;

class CampaignDeliveryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public Campaign $campaign;

    public ?CarbonInterface $scheduled_at_date;

    public array $scheduled_at;

    public Collection $links;

    protected $listeners = [
        'send-campaign' => 'send',
    ];

    protected function rules(): array
    {
        return [
            'scheduled_at' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function mount(Campaign $campaign): void
    {
        $this->authorize('send', $this->campaign);

        $this->campaign = $campaign;

        $this->scheduled_at_date = $campaign->scheduled_at ?? now()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->addHour()->startOfHour();

        $this->scheduled_at = [
            'date' => $this->scheduled_at_date->format('Y-m-d'),
            'hours' => $this->scheduled_at_date->format('H'),
            'minutes' => $this->scheduled_at_date->format('i'),
        ];

        $this->links = $this->campaign->htmlLinks()->mapWithKeys(function (string $url) {
            return [$url => null];
        });

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns'));
    }

    public function checkLinks(): void
    {
        $this->links = $this->links->map(function ($value, string $link) {
            try {
                return Http::timeout(1)->get($link)->successful();
            } catch (Throwable) {
                return false;
            }
        });
    }

    public function updatedScheduledAt()
    {
        $this->scheduled_at_date = (new DateTimeFieldRule())->parseDateTime($this->scheduled_at);
    }

    public function unschedule()
    {
        $this->campaign->markAsUnscheduled();

        $this->flash(__mc('Campaign :campaign was unscheduled', ['campaign' => $this->campaign->name]));
    }

    public function schedule()
    {
        $this->validate();

        if (! $this->campaign->isPending()) {
            $this->flash(__mc('Campaign :campaign could not be scheduled because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->scheduleToBeSentAt($this->scheduled_at_date->setTimezone(config('mailcoach.timezone') ?? config('app.timezone')));

        $this->flash(__mc('Campaign :campaign is scheduled for delivery.', ['campaign' => $this->campaign->name]));
    }

    public function send()
    {
        if (! $this->campaign->isPending()) {
            $this->flash(__mc('Campaign :campaign could not be sent because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->send();

        flash()->success(__mc('Campaign :campaign is being sent.', ['campaign' => $this->campaign->name]));

        return redirect()->route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.delivery')
            ->layout('mailcoach::app.campaigns.layouts.campaign', [
                'campaign' => $this->campaign,
                'title' => __mc('Send'),
            ]);
    }
}
