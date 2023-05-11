<?php

namespace Spatie\Mailcoach\Domain\Shared\Traits;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailUnsubscribe;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Settings\Models\PersonalAccessToken;
use Spatie\Mailcoach\Domain\Settings\Models\Setting;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Shared\Models\Upload;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

trait UsesMailcoachModels
{
    /** @return class-string<Upload> */
    public static function getUploadClass(): string
    {
        return config('mailcoach.models.upload', Upload::class);
    }

    /** @return class-string<Campaign> */
    public static function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    /** @return class-string<CampaignLink> */
    public static function getCampaignLinkClass(): string
    {
        return config('mailcoach.models.campaign_link', CampaignLink::class);
    }

    /** @return class-string<CampaignClick> */
    public static function getCampaignClickClass(): string
    {
        return config('mailcoach.models.campaign_click', CampaignClick::class);
    }

    /** @return class-string<CampaignOpen> */
    public static function getCampaignOpenClass(): string
    {
        return config('mailcoach.models.campaign_open', CampaignOpen::class);
    }

    /** @return class-string<CampaignUnsubscribe> */
    public static function getCampaignUnsubscribeClass(): string
    {
        return config('mailcoach.models.campaign_unsubscribe', CampaignUnsubscribe::class);
    }

    /** @return class-string<EmailList> */
    public static function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    /** @return class-string<Send> */
    public static function getSendClass(): string
    {
        return config('mailcoach.models.send', Send::class);
    }

    /** @return class-string<SendFeedbackItem> */
    public static function getSendFeedbackItemClass(): string
    {
        return config('mailcoach.models.send_feedback_item', SendFeedbackItem::class);
    }

    /** @return class-string<Automation> */
    public static function getAutomationClass(): string
    {
        return config('mailcoach.models.automation', Automation::class);
    }

    /** @return class-string<Action> */
    public static function getAutomationActionClass(): string
    {
        return config('mailcoach.models.automation_action', Action::class);
    }

    /** @return class-string<Trigger> */
    public static function getAutomationTriggerClass(): string
    {
        return config('mailcoach.models.automation_trigger', Trigger::class);
    }

    /** @return class-string<AutomationMail> */
    public static function getAutomationMailClass(): string
    {
        return config('mailcoach.models.automation_mail', AutomationMail::class);
    }

    /** @return class-string<AutomationMailLink> */
    public static function getAutomationMailLinkClass(): string
    {
        return config('mailcoach.models.automation_mail_link', AutomationMailLink::class);
    }

    /** @return class-string<AutomationMailClick> */
    public static function getAutomationMailClickClass(): string
    {
        return config('mailcoach.models.automation_mail_click', AutomationMailClick::class);
    }

    /** @return class-string<AutomationMailOpen> */
    public static function getAutomationMailOpenClass(): string
    {
        return config('mailcoach.models.automation_mail_open', AutomationMailOpen::class);
    }

    /** @return class-string<AutomationMailUnsubscribe> */
    public static function getAutomationMailUnsubscribeClass(): string
    {
        return config('mailcoach.models.automation_mail_unsubscribe', AutomationMailUnsubscribe::class);
    }

    /** @return class-string<Subscriber> */
    public static function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }

    /** @return class-string<Template> */
    public static function getTemplateClass(): string
    {
        return config('mailcoach.models.template', Template::class);
    }

    /** @return class-string<TransactionalMailLogItem> */
    public static function getTransactionalMailLogItemClass(): string
    {
        return config('mailcoach.models.transactional_mail_log_item', TransactionalMailLogItem::class);
    }

    /** @return class-string<TransactionalMailOpen> */
    public static function getTransactionalMailOpenClass(): string
    {
        return config('mailcoach.models.transactional_mail_open', TransactionalMailOpen::class);
    }

    /** @return class-string<TransactionalMailClick> */
    public static function getTransactionalMailClickClass(): string
    {
        return config('mailcoach.models.transactional_mail_click', TransactionalMailClick::class);
    }

    /** @return class-string<TransactionalMail> */
    public static function getTransactionalMailClass(): string
    {
        return config('mailcoach.models.transactional_mail', TransactionalMail::class);
    }

    /** @return class-string<ActionSubscriber> */
    public static function getActionSubscriberClass(): string
    {
        return config('mailcoach.models.action_subscriber', ActionSubscriber::class);
    }

    /** @return class-string<Tag> */
    public static function getTagClass(): string
    {
        return config('mailcoach.models.tag', Tag::class);
    }

    /** @return class-string<TagSegment> */
    public static function getTagSegmentClass(): string
    {
        return config('mailcoach.models.tag_segment', TagSegment::class);
    }

    /** @return class-string<SubscriberImport> */
    public static function getSubscriberImportClass(): string
    {
        return config('mailcoach.models.subscriber_import', SubscriberImport::class);
    }

    public static function getEmailListTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $emailList */
        $emailListClass = self::getEmailListClass();

        $emailList = new $emailListClass;

        return $emailList->getTable();
    }

    public static function getTransactionalMailTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $template */
        $templateClass = self::getTransactionalMailClass();

        $template = new $templateClass;

        return $template->getTable();
    }

    public static function getSubscriberTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $subscriber */
        $subscriberClass = self::getSubscriberClass();

        $subscriber = new $subscriberClass;

        return $subscriber->getTable();
    }

    public static function getTemplateTableName(): string
    {
        $templateClass = self::getTemplateClass();

        /** @var \Illuminate\Database\Eloquent\Model $template */
        $template = new $templateClass;

        return $template->getTable();
    }

    public static function getTagTableName(): string
    {
        $tagClass = self::getTagClass();

        /** @var \Illuminate\Database\Eloquent\Model $tag */
        $tag = new $tagClass;

        return $tag->getTable();
    }

    public static function getTagSegmentTableName(): string
    {
        $tagSegmentClass = self::getTagSegmentClass();

        /** @var \Illuminate\Database\Eloquent\Model $tag */
        $tagSegment = new $tagSegmentClass;

        return $tagSegment->getTable();
    }

    public static function getCampaignTableName(): string
    {
        $campaignClass = self::getCampaignClass();

        /** @var \Illuminate\Database\Eloquent\Model $campaign */
        $campaign = new $campaignClass;

        return $campaign->getTable();
    }

    public static function getAutomationMailTableName(): string
    {
        $automationMailClass = self::getAutomationMailClass();

        /** @var \Illuminate\Database\Eloquent\Model $automationMail */
        $automationMail = new $automationMailClass;

        return $automationMail->getTable();
    }

    public static function getActionSubscriberTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $actionSubscriber */
        $actionSubscriberClass = self::getActionSubscriberClass();

        $actionSubscriber = new $actionSubscriberClass;

        return $actionSubscriber->getTable();
    }

    public static function getAutomationTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $action */
        $automationClass = self::getAutomationClass();

        $automation = new $automationClass;

        return $automation->getTable();
    }

    public static function getAutomationActionTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $action */
        $actionClass = self::getAutomationActionClass();

        $action = new $actionClass;

        return $action->getTable();
    }

    public static function getAutomationTriggerTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $action */
        $className = self::getAutomationTriggerClass();

        $class = new $className;

        return $class->getTable();
    }

    public static function getCampaignLinkTableName(): string
    {
        $className = self::getCampaignLinkClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getCampaignClickTableName(): string
    {
        $className = self::getCampaignClickClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getCampaignOpenTableName(): string
    {
        $className = self::getCampaignOpenClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getCampaignUnsubscribeTableName(): string
    {
        $className = self::getCampaignUnsubscribeClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getAutomationMailLinkTableName(): string
    {
        $className = self::getAutomationMailLinkClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getAutomationMailClickTableName(): string
    {
        $className = self::getAutomationMailClickClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getAutomationMailOpenTableName(): string
    {
        $className = self::getAutomationMailOpenClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    public static function getAutomationUnsubscribeTableName(): string
    {
        $className = self::getAutomationMailUnsubscribeClass();

        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = new $className;

        return $class->getTable();
    }

    /** @return class-string<User> */
    public static function getUserClass(): string
    {
        return config('mailcoach.models.user', User::class);
    }

    /** @return class-string<PersonalAccessToken> */
    public static function getPersonalAccessTokenClass(): string
    {
        return config('mailcoach.models.personal_access_token', PersonalAccessToken::class);
    }

    /** @return class-string<Setting> */
    public static function getSettingClass(): string
    {
        return config('mailcoach.models.setting', Setting::class);
    }

    /** @return class-string<Mailer> */
    public static function getMailerClass(): string
    {
        return config('mailcoach.models.mailer', Mailer::class);
    }

    /** @return class-string<WebhookConfiguration> */
    public static function getWebhookConfigurationClass(): string
    {
        return config('mailcoach.models.webhook_configuration', WebhookConfiguration::class);
    }
}
