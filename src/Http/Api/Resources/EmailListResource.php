<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'active_subscribers_count' => (int)$this->active_subscribers_count,
            'campaigns_feed_enabled' => (bool)$this->campaigns_feed_enabled,

            'default_from_email' => $this->default_from_email,
            'default_from_name' => $this->default_from_name,

            'default_reply_to_email' => $this->default_reply_to_email,
            'default_reply_to_name' => $this->default_reply_to_name,

            'allow_form_subscriptions' => (bool)$this->allow_form_subscriptions,

            'redirect_after_subscribed' => $this->redirect_after_subscribed,
            'redirect_after_already_subscribed' => $this->redirect_after_already_subscribed,
            'redirect_after_subscription_pending' => $this->redirect_after_subscription_pending,
            'redirect_after_unsubscribed' => $this->redirect_after_unsubscribed,

            'requires_confirmation' => (bool)$this->requires_confirmation,
            'confirmation_mail_subject' => $this->confirmation_mail_subject,
            'confirmation_mail_content' => $this->confirmation_mail_content,
            'confirmation_mailable_class' => $this->confirmation_mailable_class,

            'campaign_mailer' => $this->campaign_mailer,
            'transactional_mailer' => $this->transactional_mailer,

            'send_welcome_mail' => (bool)$this->send_welcome_mail,
            'welcome_mail_subject' => $this->welcome_mail_subject,
            'welcome_mail_content' => $this->welcome_mail_content,
            'welcome_mailable_class' => $this->welcome_mailable_class,
            'welcome_mail_delay_in_minutes' => (int)$this->welcome_mail_delay_in_minutes,

            'report_recipients' => $this->report_recipients,
            'report_campaign_sent' => $this->report_campaign_sent,
            'report_campaign_summary' => $this->report_campaign_summary,
            'report_email_list_summary' => $this->report_email_list_summary,

            'email_list_summary_sent_at' => $this->email_list_summary_sent_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
