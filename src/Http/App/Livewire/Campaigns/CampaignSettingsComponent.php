<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class CampaignSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public Campaign $campaign;

    public Collection $emailLists;

    public Collection $segmentsData;

    public string $segment;

    public ?string $mailer;

    protected function rules(): array
    {
        return [
            'campaign.name' => 'required',
            'campaign.subject' => '',
            'campaign.from_email' => ['nullable', 'email:rfc'],
            'campaign.from_name' => 'nullable',
            'campaign.reply_to_email' => ['nullable', 'email:rfc'],
            'campaign.reply_to_name' => 'nullable',
            'campaign.email_list_id' => Rule::exists(self::getEmailListTableName(), 'id'),
            'campaign.utm_tags' => 'bool',
            'campaign.add_subscriber_tags' => 'bool',
            'campaign.add_subscriber_link_tags' => 'bool',
            'campaign.segment_id' => ['required_if:segment,segment'],
            'campaign.show_publicly' => ['nullable', 'bool'],
            'segment' => [Rule::in(['entire_list', 'segment'])],
        ];
    }

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->authorize('update', $this->campaign);

        $this->emailLists = self::getEmailListClass()::with('segments')->get();
        $this->segmentsData = $this->emailLists->map(fn (EmailList $emailList) => [
            'id' => $emailList->id,
            'name' => $emailList->name,
            'segments' => $emailList->segments()->orderBy('name')->pluck('name', 'id')->toArray(),
            'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
        ]);

        $this->segment = $this->campaign->notSegmenting() ? 'entire_list' : 'segment';

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns.settings', $campaign));
    }

    public function save(): void
    {
        $this->validate();

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($this->segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->campaign->usingCustomSegment()) {
            $segmentClass = $this->campaign->segment_class;
        }

        $this->campaign->fill([
            'last_modified_at' => now(),
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $this->campaign->segment_id,
        ]);

        $this->campaign->save();

        $this->campaign->update(['segment_description' => $this->campaign->getSegment()->description()]);

        $this->flash(__mc('Campaign :campaign was updated.', ['campaign' => $this->campaign->name]));
    }

    public function render(): View
    {
        $this->mailer = $this->campaign->getMailerKey();

        return view('mailcoach::app.campaigns.settings')
            ->layout('mailcoach::app.campaigns.layouts.campaign', [
                'campaign' => $this->campaign,
                'title' => __mc('Settings'),
            ]);
    }
}
