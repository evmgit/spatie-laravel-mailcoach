<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\EmailLists;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\Api\Requests\UpdateEmailListSettingsRequest;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListGeneralSettingsRequest;
use Spatie\Mailcoach\Mailcoach;

class UpdateEmailListAction
{
    public function execute(EmailList $emailList, UpdateEmailListGeneralSettingsRequest|UpdateEmailListSettingsRequest $request): EmailList
    {
        $emailList->fill([
            'name' => $request->name,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'default_reply_to_email' => $request->default_reply_to_email ?? $request->default_from_email,
            'default_reply_to_name' => $request->default_reply_to_name ?? $request->default_from_name,
            'campaign_mailer' => $request->campaign_mailer ?? Mailcoach::defaultCampaignMailer(),
            'automation_mailer' => $request->automation_mailer ?? Mailcoach::defaultAutomationMailer(),
            'transactional_mailer' => $request->transactional_mailer ?? Mailcoach::defaultTransactionalMailer(),
            'campaigns_feed_enabled' => $request->campaigns_feed_enabled ?? false,
            'report_recipients' => $request->report_recipients,
            'report_campaign_sent' => $request->report_campaign_sent ?? false,
            'report_campaign_summary' => $request->report_campaign_summary ?? false,
            'report_email_list_summary' => $request->report_email_list_summary ?? false,
            'allow_form_subscriptions' => $request->allow_form_subscriptions ?? false,
            'requires_confirmation' => $request->requires_confirmation ?? false,
            'redirect_after_subscribed' => $request->redirect_after_subscribed ?? null,
            'redirect_after_already_subscribed' => $request->redirect_after_already_subscribed ?? null,
            'redirect_after_subscription_pending' => $request->redirect_after_subscription_pending ?? null,
            'redirect_after_unsubscribed' => $request->redirect_after_unsubscribed ?? null,
        ]);

        if ($request instanceof UpdateEmailListSettingsRequest) {
            $emailList->fill([
                'confirmation_mail_id' => $request->sendDefaultConfirmationMail() ? null : $request->confirmation_mail_id,
            ]);

            $emailList->allowedFormSubscriptionTags()->sync($request->allowedFormSubscriptionTags());
        }

        $emailList->save();

        return $emailList->refresh();
    }
}
