<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class ListSummaryComponent extends Component
{
    use UsesMailcoachModels;

    public EmailList $emailList;

    // Filters
    protected $queryString = ['start', 'end'];

    public ?string $start = null;

    public ?string $end = null;

    // Counts
    public int $totalSubscriptionsCount;

    public int $totalUnsubscribeCount;

    public int $startSubscriptionsCount;

    public int $startUnsubscribeCount;

    // Chart
    public ?Collection $stats = null;

    public bool $readyToLoad = false;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        $this->start ??= now()->subDays(29)->format('Y-m-d');
        $this->end ??= now()->format('Y-m-d');

        $this->totalSubscriptionsCount = $this->emailList->subscribers()->count();
        $this->totalUnsubscribeCount = $this->emailList->allSubscribers()->unsubscribed()->count();

        app(MainNavigation::class)->activeSection()->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList));
    }

    public function updatedStart($newStart)
    {
        if (Date::parse($newStart) > Date::parse($this->end)) {
            $this->start = $this->end;
        }
    }

    public function updatedEnd($newEnd)
    {
        if (Date::parse($newEnd) > Date::now()) {
            $this->end = Date::now()->format('Y-m-d');
        }

        if (Date::parse($newEnd) < Date::parse($this->start)) {
            $this->end = $this->start;
        }
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render(): View
    {
        $this->startSubscriptionsCount = $this->emailList->subscribers()
            ->where('subscribed_at', '<', $this->start)
            ->count();

        $this->startUnsubscribeCount = $this->emailList->allSubscribers()
            ->unsubscribed()
            ->where('unsubscribed_at', '>', $this->start)
            ->count();

        if ($this->readyToLoad) {
            $this->stats = $this->createStats();
        }

        return view('mailcoach::app.emailLists.summary', [
            'totalSubscriptionsCount' => $this->totalSubscriptionsCount(),
            'growthRate' => $this->growthRate(),
            'churnRate' => $this->churnRate(),
            'averageOpenRate' => $this->averageOpenRate(),
            'averageClickRate' => $this->averageClickRate(),
            'averageUnsubscribeRate' => $this->averageUnsubscribeRate(),
            'averageBounceRate' => $this->averageBounceRate(),
        ])->layout('mailcoach::app.emailLists.layouts.emailList', [
            'title' => __mc('Performance'),
            'emailList' => $this->emailList,
            'hideCard' => true,
        ]);
    }

    protected function createStats(): Collection
    {
        $prefix = DB::getTablePrefix();
        $subscriberTable = $prefix.$this->getSubscriberTableName().(DB::connection() instanceof MySqlConnection ? ' USE INDEX (email_list_subscribed_index)' : '');

        $start = Date::parse($this->start);
        $end = Date::parse($this->end);

        $diff = $start->diffInSeconds($end);
        $interval = match (true) {
            $diff > 60 * 60 * 24 * 2 => 'day', // > 7 days
            default => 'hour',
        };

        $start = $start->startOf($interval === 'hour' ? 'day' : $interval);
        $end = $end->endOf($interval === 'hour' ? 'day' : $interval);

        $dateFormat = match ($interval) {
            'hour' => '%Y-%m-%d %H:%I',
            'day' => '%Y-%m-%d',
        };

        $subscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as subscribed_count, DATE_FORMAT(subscribed_at, \"{$dateFormat}\") as subscribed_day")
            ->where('email_list_id', $this->emailList->id)
            ->whereBetween('subscribed_at', [$start, $end])
            ->whereNull('unsubscribed_at')
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        $unsubscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as unsubscribe_count, DATE_FORMAT(unsubscribed_at, \"{$dateFormat}\") as unsubscribe_day")
            ->where('email_list_id', $this->emailList->id)
            ->whereBetween('unsubscribed_at', [$start, $end])
            ->whereNotNull('unsubscribed_at')
            ->orderBy('unsubscribe_day')
            ->groupBy('unsubscribe_day')
            ->get();

        $subscriberTotal = $this->startSubscriptionsCount;
        $subscribers = collect($subscribes)->map(function ($result) use ($interval, &$subscriberTotal, $unsubscribes) {
            $subscriberTotal += $result->subscribed_count;
            $unsubscribeCount = $unsubscribes->where('unsubscribe_day', $result->subscribed_day)->first();
            $subscriberTotal -= optional($unsubscribeCount)->unsubscribe_count ?? 0;

            return [
                'label' => match ($interval) {
                    'hour' => Carbon::createFromFormat('Y-m-d H:i', $result->subscribed_day)->startOf($interval)->format('y M d H:i'),
                    'day' => Carbon::createFromFormat('Y-m-d', $result->subscribed_day)->startOf($interval)->format('y M d'),
                },
                'subscribers' => $subscriberTotal,
                'subscribes' => $result->subscribed_count,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count,
            ];
        });

        $lastStats = [
            'subscribers' => $this->startSubscriptionsCount,
        ];

        return collect(CarbonPeriod::create($start, '1 '.$interval, $end))->map(function (CarbonInterface $day) use ($interval, $subscribers, &$lastStats) {
            $label = match ($interval) {
                'hour' => $day->startOf($interval)->format('y M d H:i'),
                'day' => $day->startOf($interval)->format('y M d'),
            };

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            return $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'subscribes' => 0,
                'unsubscribes' => 0,
            ];
        });
    }

    public function averageOpenRate(): float
    {
        return $this->emailList->campaigns()->average('open_rate') / 100;
    }

    public function averageClickRate(): float
    {
        return $this->emailList->campaigns()->average('click_rate') / 100;
    }

    public function averageUnsubscribeRate(): float
    {
        return $this->emailList->campaigns()->average('unsubscribe_rate') / 100;
    }

    public function averageBounceRate(): float
    {
        return $this->emailList->campaigns()->average('bounce_rate') / 100;
    }

    public function growthRate(): float
    {
        $start = $this->startSubscriptionsCount > 0
            ? $this->startSubscriptionsCount
            : 1;

        // Percent Change = 100 × (Present or Future Value – Past or Present Value) / Past or Present Value
        return round(100 * ($this->totalSubscriptionsCount - $start) / $start, 2);
    }

    public function churnRate(): float
    {
        if ($this->totalSubscriptionsCount === 0) {
            return 0;
        }

        return round($this->startUnsubscribeCount / $this->totalSubscriptionsCount, 2);
    }

    public function totalSubscriptionsCount(): int
    {
        return $this->totalSubscriptionsCount;
    }

    public function startSubscriptionsCount(): int
    {
        return $this->startSubscriptionsCount;
    }

    public function totalUnsubscribeCount(): int
    {
        return $this->totalUnsubscribeCount;
    }

    public function startUnsubscribeCount(): int
    {
        return $this->startUnsubscribeCount;
    }
}
