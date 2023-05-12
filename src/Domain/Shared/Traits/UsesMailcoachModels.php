<?php

namespace Spatie\Mailcoach\Domain\Shared\Traits;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
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
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

trait UsesMailcoachModels
{
    public static function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    public static function getCampaignLinkClass(): string
    {
        return config('mailcoach.models.campaign_link', CampaignLink::class);
    }

    public static function getCampaignClickClass(): string
    {
        return config('mailcoach.models.campaign_click', CampaignClick::class);
    }

    public static function getCampaignOpenClass(): string
    {
        return config('mailcoach.models.campaign_open', CampaignOpen::class);
    }

    public static function getCampaignUnsubscribeClass(): string
    {
        return config('mailcoach.models.campaign_unsubscribe', CampaignUnsubscribe::class);
    }

    public static function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    public function getSendClass(): string
    {
        return config('mailcoach.models.send', Send::class);
    }

    public static function getAutomationClass(): string
    {
        return config('mailcoach.models.automation', Automation::class);
    }

    public static function getAutomationActionClass(): string
    {
        return config('mailcoach.models.automation_action', Action::class);
    }

    public static function getAutomationTriggerClass(): string
    {
        return config('mailcoach.models.automation_trigger', Trigger::class);
    }

    public static function getAutomationMailClass(): string
    {
        return config('mailcoach.models.automation_mail', AutomationMail::class);
    }

    public static function getAutomationMailLinkClass(): string
    {
        return config('mailcoach.models.automation_mail_link', AutomationMailLink::class);
    }

    public static function getAutomationMailClickClass(): string
    {
        return config('mailcoach.models.automation_mail_click', AutomationMailClick::class);
    }

    public static function getAutomationMailOpenClass(): string
    {
        return config('mailcoach.models.automation_mail_open', AutomationMailOpen::class);
    }

    public static function getAutomationMailUnsubscribeClass(): string
    {
        return config('mailcoach.models.automation_mail_unsubscribe', AutomationMailUnsubscribe::class);
    }

    public static function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }

    public static function getTemplateClass(): string
    {
        return config('mailcoach.models.template', Template::class);
    }

    public function getTransactionalMailClass(): string
    {
        return config('mailcoach.models.transactional_mail', TransactionalMail::class);
    }

    public function getTransactionalMailTemplateClass(): string
    {
        return config('mailcoach.models.transactional_mail_template', TransactionalMailTemplate::class);
    }

    public static function getActionSubscriberClass(): string
    {
        return config('mailcoach.models.action_subscriber', ActionSubscriber::class);
    }

    public static function getEmailListTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $emailList */
        $emailListClass = self::getEmailListClass();

        $emailList = new $emailListClass;

        return $emailList->getTable();
    }

    public function getSubscriberTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $subscriber */
        $subscriberClass = $this->getSubscriberClass();

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
}
