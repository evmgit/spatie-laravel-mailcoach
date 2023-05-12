<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Support\Svg\BezierCurve;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class EmailListSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected CarbonImmutable $start;

    protected EmailList $emailList;

    protected Collection $stats;

    protected int $subscribersLimit;

    protected int $totalSubscriptionsCount;

    protected int $startSubscriptionsCount;

    protected int $totalUnsubscribeCount;

    protected int $startUnsubscribeCount;

    public function __construct(EmailList $emailList)
    {
        $this->start = now()->subDays(29)->startOfDay()->toImmutable();

        $this->emailList = $emailList;

        $this->totalSubscriptionsCount = $this->emailList->subscribers()->count();

        $this->totalUnsubscribeCount = $this->emailList->allSubscribers()->unsubscribed()->count();

        $this->startSubscriptionsCount = $this->emailList->subscribers()
            ->where('subscribed_at', '<', $this->start)
            ->count();

        $this->startUnsubscribeCount = $this->emailList->allSubscribers()->unsubscribed()->where('unsubscribed_at', '>', $this->start)->count();

        $this->stats = $this->createStats();

        $this->subscribersLimit = (ceil($this->stats->max('subscribers') * 1.1 / 10) * 10) ?: 1;
    }

    public function activeFilter(): string
    {
        return request()->get('filter')['status'] ?? '';
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

    public function emailList(): EmailList
    {
        return $this->emailList;
    }

    public function stats(): Collection
    {
        return $this->stats;
    }

    public function subscribersLimit(): int
    {
        return $this->subscribersLimit;
    }

    public function growthRate(): float
    {
        if ($this->startSubscriptionsCount === 0) {
            return 0;
        }

        // Percent Change = 100 × (Present or Future Value – Past or Present Value) / Past or Present Value
        return round(100 * ($this->totalSubscriptionsCount - $this->startSubscriptionsCount) / $this->startSubscriptionsCount, 2);
    }

    public function churnRate(): float
    {
        if ($this->totalSubscriptionsCount === 0) {
            return 0;
        }

        $unsubscribesSinceStart = $this->emailList
            ->allSubscribers()
            ->unsubscribed()
            ->where('unsubscribed_at', '>', $this->start)
            ->count();

        return round($unsubscribesSinceStart / $this->totalSubscriptionsCount, 2);
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

    public function subscribersPath(): string
    {
        $points = $this->stats
            ->pluck('subscribers')
            ->map(function (int $subscribers, int $index) {
                return [$index, 100 - ($subscribers / $this->subscribersLimit) * 100];
            })
            ->toArray();

        return (new BezierCurve([[0, 100], ...$points, [30, 100]]))->toPath();
    }

    protected function createStats(): Collection
    {
        $subscriberTotal = $this->startSubscriptionsCount;

        $prefix = DB::getTablePrefix();
        $subscriberTable = $prefix . $this->getSubscriberTableName() . (DB::connection() instanceof MySqlConnection ? ' USE INDEX (email_list_subscribed_index)' : '');

        $subscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as subscribed_count, date(subscribed_at) as subscribed_day")
            ->where('email_list_id', $this->emailList->id)
            ->where('subscribed_at', '>=', $this->start)
            ->where('subscribed_at', '<=', now())
            ->whereNull('unsubscribed_at')
            ->orderBy('subscribed_day')
            ->groupBy('subscribed_day')
            ->get();

        $unsubscribes = DB::table(DB::raw($subscriberTable))
            ->selectRaw("count(*) as unsubscribe_count, date(unsubscribed_at) as unsubscribe_day")
            ->where('email_list_id', $this->emailList->id)
            ->where('unsubscribed_at', '>=', $this->start)
            ->where('unsubscribed_at', '<=', now())
            ->whereNotNull('unsubscribed_at')
            ->orderBy('unsubscribe_day')
            ->groupBy('unsubscribe_day')
            ->get();

        $subscribers = collect($subscribes)->map(function ($result) use (&$subscriberTotal, $unsubscribes) {
            $subscriberTotal += $result->subscribed_count;
            $unsubscribeCount = $unsubscribes->where('unsubscribe_day', $result->subscribed_day)->first();

            return [
                'label' => Carbon::createFromFormat('Y-m-d', $result->subscribed_day)->format('M d'),
                'subscribers' => $subscriberTotal,
                'unsubscribes' => optional($unsubscribeCount)->unsubscribe_count,
            ];
        });

        $lastStats = $subscribers->first();

        return collect(CarbonPeriod::create($this->start, '1 day', now()))->map(function (\Carbon\Carbon $day) use ($subscribers, &$lastStats) {
            $label = $day->format('M d');

            $stats = $subscribers->firstWhere('label', $label);

            if ($stats) {
                $lastStats = $stats;
            }

            return $subscribers->firstWhere('label', $label) ?: [
                'label' => $label,
                'subscribers' => $lastStats['subscribers'] ?? 0,
                'unsubscribes' => $lastStats['unsubscribes'] ?? 0,
            ];
        });
    }
}
