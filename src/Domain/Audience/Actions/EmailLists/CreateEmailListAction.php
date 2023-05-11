<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\EmailLists;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Mailcoach;

class CreateEmailListAction
{
    public function execute(EmailList $emailList, array $data): EmailList
    {
        $emailList->fill([
            'name' => $data['name'],
            'default_from_email' => $data['default_from_email'],
            'default_from_name' => $data['default_from_name'] ?? null,
            'campaign_mailer' => $data['campaign_mailer'] ?? Mailcoach::defaultCampaignMailer(),
            'automation_mailer' => $data['automation_mailer'] ?? Mailcoach::defaultAutomationMailer(),
            'transactional_mailer' => $data['transactional_mailer'] ?? Mailcoach::defaultTransactionalMailer(),
            'requires_confirmation' => $data['requires_confirmation'] ?? true,
            'campaigns_feed_enabled' => false,
            'website_title' => $data['name'],
            'website_slug' => Str::slug($data['name']),
        ]);

        $emailList->save();

        return $emailList->refresh();
    }
}
