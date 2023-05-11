<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DashboardComponent extends Component
{
    use UsesMailcoachModels;

    public ?int $recentSubscribers = null;

    public ?Campaign $latestCampaign;

    public bool $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if ($this->readyToLoad) {
            $this->recentSubscribers = self::getSubscriberClass()::subscribed()->whereBetween('subscribed_at', [now()->subMonth(), now()])->count();
        }

        $this->latestCampaign = self::getCampaignClass()::sent()->latest()->first();

        return view('mailcoach::app.dashboard')
            ->layout('mailcoach::app.layouts.app', [
                'originTitle' => __mc('Dashboard'),
                'hideCard' => true,
                'hideBreadcrumbs' => true,
            ]);
    }

    public function abbreviateNumber(int $number): string
    {
        if ($number >= 0 && $number < 1000) {
            $format = floor($number);
            $suffix = '';
        } elseif ($number >= 1000 && $number < 1000000) {
            $format = floor($number / 1000);
            $suffix = 'K+';
        } elseif ($number >= 1000000 && $number < 1000000000) {
            $format = floor($number / 1000000);
            $suffix = 'M+';
        } elseif ($number >= 1000000000 && $number < 1000000000000) {
            $format = floor($number / 1000000000);
            $suffix = 'B+';
        } else {
            $format = floor($number / 1000000000000);
            $suffix = 'T+';
        }

        return ! empty($format.$suffix) ? $format.$suffix : 0;
    }
}
