<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportCampaignsJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedCampaigns
     */
    public function __construct(protected string $path, protected array $selectedCampaigns)
    {
    }

    public function name(): string
    {
        return 'Campaigns';
    }

    public function execute(): void
    {
        $campaigns = DB::table(self::getCampaignTableName())
            ->select(
                self::getCampaignTableName().'.*',
                DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'),
                DB::raw(self::getTagSegmentTableName().'.name as segment_name'),
            )
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getCampaignTableName().'.email_list_id')
            ->leftJoin(self::getTagSegmentTableName(), self::getTagSegmentTableName().'.id', '=', self::getCampaignTableName().'.segment_id')
            ->whereIn(self::getCampaignTableName().'.id', $this->selectedCampaigns)
            ->get();

        $this->writeFile('campaigns.csv', $campaigns);
        $this->addMeta('campaigns_count', $campaigns->count());

        $campaignLinks = DB::table(self::getCampaignLinkTableName())
            ->whereIn('campaign_id', $this->selectedCampaigns)
            ->get();

        $this->writeFile('campaign_links.csv', $campaignLinks);
        $this->addMeta('campaign_links_count', $campaignLinks->count());
    }
}
