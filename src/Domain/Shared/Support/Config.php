<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;

class Config
{
    public static function getCampaignActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.campaigns.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getSharedActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.shared.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getAutomationActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.automation.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getAudienceActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.audience.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getTransactionalActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.transactional.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    protected static function getActionClass(?string $configuredClass, string $actionName, string $actionClass): object
    {
        if (is_null($configuredClass)) {
            $configuredClass = $actionClass;
        }

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass, $actionClass);
        }

        return resolve($configuredClass);
    }

    public static function getQueueConnection(): ?string
    {
        return config('mailcoach.queue_connection') ?? env('QUEUE_CONNECTION');
    }
}
