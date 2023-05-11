<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignStatisticsComponent extends Component
{
    use UsesMailcoachModels;

    public Campaign $campaign;

    // Chart
    public Collection $stats;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function render(): View
    {
        $this->stats = $this->createStats();

        return view('mailcoach::app.campaigns.partials.chart');
    }

    protected function createStats(): Collection
    {
        if (! $this->campaign->wasAlreadySent()) {
            return collect();
        }

        if (! $this->campaign->opens()->count()) {
            return collect();
        }

        $start = $this->campaign->sent_at->startOfHour()->toImmutable();

        if ($this->campaign->open_count > 0) {
            $firstOpenCreatedAt = $this->campaign->opens()->first()?->created_at;

            if ($firstOpenCreatedAt && $firstOpenCreatedAt < $start) {
                $start = $firstOpenCreatedAt->startOfHour()->toImmutable();
            }
        }

        $end = $this->campaign->opens()->latest('created_at')->first('created_at')?->created_at;
        $limit = $start->copy()->addHours(24 * 2);

        if (is_null($end) || $limit->isBefore($end)) {
            $end = $limit;
        }

        $campaignOpenTable = self::getCampaignOpenTableName();
        $campaignClickTable = self::getCampaignClickTableName();
        $campaignLinkTable = self::getCampaignLinkTableName();

        $opensPerMinute = DB::table($campaignOpenTable)
            ->where('campaign_id', $this->campaign->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as minute, COUNT(*) as opens')
            ->groupBy('minute')
            ->get();

        $clicksPerMinute = DB::table($campaignClickTable)
            ->join($campaignLinkTable, 'campaign_link_id', '=', $campaignLinkTable.'.id')
            ->where('campaign_id', $this->campaign->id)
            ->selectRaw("DATE_FORMAT({$campaignClickTable}.created_at, \"%Y-%m-%d %H:%i\") as minute, COUNT(*) as clicks")
            ->groupBy('minute')
            ->get();

        return collect(CarbonPeriod::create($start, '10 minutes', $end))->map(function (CarbonInterface $minutes) use ($opensPerMinute, $clicksPerMinute) {
            $minutes = $minutes->toImmutable();

            return [
                'label' => $minutes->isoFormat('dd HH:mm'),
                'opens' => $opensPerMinute->whereBetween('minute', [$minutes->format('Y-m-d H:i'), $minutes->addMinutes(10)->format('Y-m-d H:i')])->sum('opens'),
                'clicks' => $clicksPerMinute->whereBetween('minute', [$minutes->format('Y-m-d H:i'), $minutes->addMinutes(10)->format('Y-m-d H:i')])->sum('clicks'),
            ];
        });
    }
}
