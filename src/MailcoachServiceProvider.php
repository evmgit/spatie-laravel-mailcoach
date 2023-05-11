<?php

namespace Spatie\Mailcoach;

use Exception;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\QueryException;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Livewire;
use LivewireUI\Spotlight\Spotlight;
use Spatie\Flash\Flash;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\ReplacerHelpTextsComponent;
use Spatie\Mailcoach\Domain\Audience\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Domain\Audience\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Domain\Audience\Livewire\EmailListStatistics;
use Spatie\Mailcoach\Domain\Automation\Commands\CalculateAutomationMailStatisticsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\SendAutomationMailsCommand;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Automation\Listeners\AddAutomationMailClickedTag;
use Spatie\Mailcoach\Domain\Automation\Listeners\AddAutomationMailOpenedTag;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AutomationMailActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\ConditionActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\SplitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationBuilder;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\DateTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\NoTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagAddedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagRemovedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\WebhookTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredByEvents;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignMailsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignClickedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignOpenedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SetWebhookCallProcessedAt;
use Spatie\Mailcoach\Domain\Settings\Commands\MakeUserCommand;
use Spatie\Mailcoach\Domain\Settings\Commands\PrepareGitIgnoreCommand;
use Spatie\Mailcoach\Domain\Settings\Commands\PublishCommand;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\Policies\PersonalAccessTokenPolicy;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Settings\Support\AppConfiguration\AppConfiguration;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\EditorConfiguration;
use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\DeleteOldExportsCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottleCache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateListComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSegmentComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSubscriberComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateTagComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailersComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboardingComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettingsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummaryComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentSubscribersComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberImportsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberSendsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\TagComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\TagsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Audience\WebsiteComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActionsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicksComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpensComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutboxComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettingsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummaryComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribesComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettingsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomationComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomationMailComponent;
use Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomationComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicksComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpensComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutboxComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettingsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignStatisticsComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummaryComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribesComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateCampaignComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateTemplateComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplateComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplatesComponent;
use Spatie\Mailcoach\Http\App\Livewire\DashboardChart;
use Spatie\Mailcoach\Http\App\Livewire\DashboardComponent;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Http\App\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Http\App\Livewire\SendTestComponent;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\AutomationEmailsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\AutomationsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CampaignsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateAutomationCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateAutomationMailCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateCampaignCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateListCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateTransactionalTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\HomeCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ListsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowAutomationCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowAutomationMailCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowCampaignCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowListCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowTransactionalTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TemplatesCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TransactionalLogCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TransactionalTemplatesCommand;
use Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\CreateTransactionalTemplateComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContentComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailLogItemsComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformanceComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResendComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailsComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContentComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent;
use Spatie\Mailcoach\Http\App\Middleware\SetMailcoachDefaults;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\HealthViewComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\WebsiteStyleComposer;
use Spatie\Mailcoach\Http\Livewire\CreateMailerComponent;
use Spatie\Mailcoach\Http\Livewire\CreateUserComponent;
use Spatie\Mailcoach\Http\Livewire\CreateWebhookComponent;
use Spatie\Mailcoach\Http\Livewire\EditMailerComponent;
use Spatie\Mailcoach\Http\Livewire\EditorSettingsComponent;
use Spatie\Mailcoach\Http\Livewire\EditUserComponent;
use Spatie\Mailcoach\Http\Livewire\EditWebhookComponent;
use Spatie\Mailcoach\Http\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Mailgun\MailgunSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Postmark\PostmarkSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\SendGrid\SendGridSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Sendinblue\SendinblueSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Ses\SesSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailConfiguration\Smtp\SmtpSetupWizardComponent;
use Spatie\Mailcoach\Http\Livewire\MailersComponent;
use Spatie\Mailcoach\Http\Livewire\PasswordComponent;
use Spatie\Mailcoach\Http\Livewire\ProfileComponent;
use Spatie\Mailcoach\Http\Livewire\TokensComponent;
use Spatie\Mailcoach\Http\Livewire\UsersComponent;
use Spatie\Mailcoach\Http\Livewire\WebhooksComponent;
use Spatie\Navigation\Helpers\ActiveUrlChecker;

class MailcoachServiceProvider extends PackageServiceProvider
{
    use UsesMailcoachModels;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailcoach')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                'create_mailcoach_tables',
                'create_media_table',
                'create_webhook_calls_table',
            ])
            ->hasCommands([
                CalculateStatisticsCommand::class,
                CalculateAutomationMailStatisticsCommand::class,
                SendAutomationMailsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignMailsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                CleanupProcessedFeedbackCommand::class,
                RunAutomationActionsCommand::class,
                RunAutomationTriggersCommand::class,
                CheckLicenseCommand::class,
                DeleteOldExportsCommand::class,
                PublishCommand::class,
                MakeUserCommand::class,
                PrepareGitIgnoreCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Version::class, fn () => new Version());

        $this->app->scoped(MainNavigation::class, function () {
            return new MainNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SettingsNavigation::class, function () {
            return new SettingsNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SimpleThrottle::class, function () {
            $cache = cache()->store(config('mailcoach.shared.throttling.cache_store'));

            $simpleThrottleCache = new SimpleThrottleCache($cache);

            return SimpleThrottle::create($simpleThrottleCache);
        });
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
                $this->package->basePath('/../resources/images') => public_path("vendor/{$this->package->shortName()}/images"),
            ], "{$this->package->shortName()}-assets");
        }

        $this
            ->bootCarbon()
            ->bootConfig()
            ->bootGate()
            ->bootFlash()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers()
            ->registerApiGuard()
            ->bootEncryption()
            ->bootSpotlight();
    }

    protected function bootCarbon(): static
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro(
            'toMailcoachFormat',
            /** @phpstan-ignore-next-line */
            fn () => self::this()->copy()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->format($mailcoachFormat)
        );

        return $this;
    }

    protected function bootConfig(): static
    {
        try {
            self::getMailerClass()::registerAllConfigValues();
        } catch (QueryException) {
            // Do nothing as table probably doesn't exist
        }

        app(AppConfiguration::class)->registerConfigValues();
        app(EditorConfiguration::class)->registerConfigValues();

        return $this;
    }

    protected function bootSupportMacros(): static
    {
        if (! Collection::hasMacro('paginate')) {
            Collection::macro('paginate', function (int $perPage = 15, string $pageName = 'page', int $page = null, int $total = null, array $options = []): LengthAwarePaginator {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

                /** @var Collection $this */
                $results = $this->forPage($page, $perPage)->values();

                $total = $total ?: $this->count();

                $options += [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ];

                return new LengthAwarePaginator($results, $total, $perPage, $page, $options);
            });
        }

        if (! Str::hasMacro('shortNumber')) {
            Str::macro('shortNumber', function (int $number, int $decimals = 1) {
                if ($number < 1_000) {
                    $format = number_format($number, $decimals);
                    $suffix = '';
                } elseif ($number < 1_000_000) {
                    $format = number_format(floor($number / 100) / 10, $decimals);
                    $suffix = 'K';
                } elseif ($number < 1_000_000_000) {
                    $format = number_format(floor($number / 100000) / 10, $decimals);
                    $suffix = 'M';
                } else {
                    return '🤯';
                }

                if ($decimals > 0) {
                    $dotzero = '.'.str_repeat('0', $decimals);
                    $format = str_replace($dotzero, '', $format);
                }

                return $format.$suffix;
            });
        }

        return $this;
    }

    protected function bootGate(): static
    {
        Gate::define('viewMailcoach', fn (Authorizable $user) => true);

        Gate::policy(self::getPersonalAccessTokenClass(), PersonalAccessTokenPolicy::class);

        return $this;
    }

    protected function bootFlash(): static
    {
        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);

        return $this;
    }

    protected function bootRoutes(): static
    {
        // Audience
        Route::model('emailList', self::getEmailListClass());
        Route::model('subscriber', self::getSubscriberClass());
        Route::model('subscriberImport', self::getSubscriberImportClass());
        Route::model('tagSegment', self::getTagSegmentClass());
        Route::model('action', self::getAutomationActionClass());

        // Automation
        Route::model('action', self::getAutomationActionClass());
        Route::model('actionSubscriber', self::getActionSubscriberClass());
        Route::model('automation', self::getAutomationClass());
        Route::model('automationMail', self::getAutomationMailClass());
        Route::model('automationMailClick', self::getAutomationMailClickClass());
        Route::model('automationMailLink', self::getAutomationMailLinkClass());
        Route::model('automationMailOpen', self::getAutomationMailOpenClass());
        Route::model('automationMailUnsubscribe', self::getAutomationMailUnsubscribeClass());
        Route::model('trigger', self::getAutomationTriggerClass());

        // Campaign
        Route::model('campaign', self::getCampaignClass());
        Route::model('campaignClick', self::getCampaignClickClass());
        Route::model('campaignLink', self::getCampaignLinkClass());
        Route::model('campaignOpen', self::getCampaignOpenClass());
        Route::model('campaignUnsubscribe', self::getCampaignUnsubscribeClass());
        Route::model('template', self::getTemplateClass());

        // Settings
        Route::model('mailer', self::getMailerClass());
        Route::model('personalAccessToken', self::getPersonalAccessTokenClass());
        Route::model('setting', self::getSettingClass());
        Route::model('user', self::getUserClass());
        Route::model('webhookConfiguration', self::getWebhookConfigurationClass());

        // Shared
        Route::model('send', self::getSendClass());
        Route::model('sendFeedbackItem', self::getSendFeedbackItemClass());
        Route::model('upload', self::getUploadClass());

        // Transactional
        Route::model('transactionalMail', self::getTransactionalMailLogItemClass());
        Route::model('transactionalMailClick', self::getTransactionalMailClickClass());
        Route::model('transactionalMailOpen', self::getTransactionalMailOpenClass());
        Route::model('transactionalMailTemplate', self::getTransactionalMailClass());

        Route::macro('mailcoach', function (
            string $url = '',
            bool $registerFeedback = true,
            bool $registerAuth = true) {
            if ($registerFeedback) {
                Route::sesFeedback('ses-feedback');
                Route::mailgunFeedback('mailgun-feedback');
                Route::sendgridFeedback('sendgrid-feedback');
                Route::postmarkFeedback('postmark-feedback');
                Route::sendinblueFeedback('sendinblue-feedback');
            }

            Route::prefix($url)->middleware([SetMailcoachDefaults::class])->group(function () use ($registerAuth) {
                if ($registerAuth) {
                    Route::prefix('')
                        ->middleware('web')
                        ->group(__DIR__.'/../routes/auth.php');
                }

                Route::prefix('')
                    ->group(__DIR__.'/../routes/mailcoach-public-api.php');

                Route::prefix('')
                    ->middleware(config('mailcoach.middleware')['web'])
                    ->group(__DIR__.'/../routes/mailcoach-ui.php');

                Route::prefix('api')
                    ->middleware(config('mailcoach.middleware')['api'])
                    ->group(__DIR__.'/../routes/mailcoach-api.php');

                /*
                 * The website routes should be registered last, so that
                 * they don't eat up other routes
                 */

                Route::prefix(config('mailcoach.email_list_website_prefix', 'archive'))
                    ->middleware('web')
                    ->group(__DIR__.'/../routes/mailcoach-email-list-website.php');
            });

            Route::mailcoachEditor('mailcoachEditor');
            Route::get($url, '\\'.HomeController::class)->name('mailcoach.home');
        });

        return $this;
    }

    protected function bootViews(): static
    {
        View::composer('mailcoach::app.layouts.partials.footer', FooterComposer::class);
        View::composer('mailcoach::app.layouts.partials.health', HealthViewComposer::class);
        View::composer('mailcoach::app.layouts.partials.health-tiles', HealthViewComposer::class);
        View::composer('mailcoach::emailListWebsite.partials.style', WebsiteStyleComposer::class);

        if (config('mailcoach.views.use_blade_components', true)) {
            $this->bootBladeComponents();
        }

        $this->bootLivewireComponents();

        return $this;
    }

    protected function bootBladeComponents(): static
    {
        Blade::component('mailcoach::app.components.form.checkboxField', 'mailcoach::checkbox-field');
        Blade::component('mailcoach::app.components.form.radioField', 'mailcoach::radio-field');
        Blade::component('mailcoach::app.components.form.formButton', 'mailcoach::form-button');
        Blade::component('mailcoach::app.components.form.formButtons', 'mailcoach::form-buttons');
        Blade::component('mailcoach::app.components.form.confirmButton', 'mailcoach::confirm-button');
        Blade::component('mailcoach::app.components.form.selectField', 'mailcoach::select-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'mailcoach::tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'mailcoach::text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'mailcoach::html-field');
        Blade::component('mailcoach::app.components.form.markdownField', 'mailcoach::markdown-field');
        Blade::component('mailcoach::app.components.form.colorField', 'mailcoach::color-field');
        Blade::component('mailcoach::app.components.form.textArea', 'mailcoach::textarea-field');

        Blade::component('mailcoach::app.components.form.templateChooser', 'mailcoach::template-chooser');

        Blade::component('mailcoach::app.components.form.dateField', 'mailcoach::date-field');
        Blade::component('mailcoach::app.components.form.fieldset', 'mailcoach::fieldset');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');
        Blade::component('mailcoach::app.components.modal.previewModal', 'mailcoach::preview-modal');

        Blade::component('mailcoach::app.components.card', 'mailcoach::card');
        Blade::component('mailcoach::app.components.tile', 'mailcoach::tile');

        Blade::component('mailcoach::app.components.dataTable', 'mailcoach::data-table');
        Blade::component('mailcoach::app.components.table.tableStatus', 'mailcoach::table-status');
        Blade::component('mailcoach::app.components.table.th', 'mailcoach::th');

        Blade::component('mailcoach::app.components.filters.filters', 'mailcoach::filters');
        Blade::component('mailcoach::app.components.filters.filter', 'mailcoach::filter');

        Blade::component('mailcoach::app.components.search', 'mailcoach::search');
        Blade::component('mailcoach::app.components.statistic', 'mailcoach::statistic');
        Blade::component('mailcoach::app.components.iconLabel', 'mailcoach::icon-label');
        Blade::component('mailcoach::app.components.healthLabel', 'mailcoach::health-label');
        Blade::component('mailcoach::app.components.roundedIcon', 'mailcoach::rounded-icon');

        Blade::component('mailcoach::app.components.navigation.main', 'mailcoach::main-navigation');
        Blade::component('mailcoach::app.components.navigation.root', 'mailcoach::navigation');
        Blade::component('mailcoach::app.components.navigation.item', 'mailcoach::navigation-item');
        Blade::component('mailcoach::app.components.navigation.group', 'mailcoach::navigation-group');
        Blade::component('mailcoach::app.components.navigation.tabs', 'mailcoach::navigation-tabs');

        Blade::component('mailcoach::app.components.alert.info', 'mailcoach::info');
        Blade::component('mailcoach::app.components.alert.help', 'mailcoach::help');
        Blade::component('mailcoach::app.components.alert.warning', 'mailcoach::warning');
        Blade::component('mailcoach::app.components.alert.error', 'mailcoach::error');
        Blade::component('mailcoach::app.components.alert.success', 'mailcoach::success');

        Blade::component('mailcoach::app.components.counter', 'mailcoach::counter');
        Blade::component('mailcoach::app.components.addressDefinition', 'mailcoach::address-definition');
        Blade::component('mailcoach::app.components.webview', 'mailcoach::web-view');

        Blade::component('mailcoach::app.components.button.primary', 'mailcoach::button');
        Blade::component('mailcoach::app.components.button.secondary', 'mailcoach::button-secondary');
        Blade::component('mailcoach::app.components.button.cancel', 'mailcoach::button-cancel');

        Blade::component('mailcoach::app.components.editorButtons', 'mailcoach::editor-buttons');
        Blade::component('mailcoach::app.components.editorFields', 'mailcoach::editor-fields');
        Blade::component(ReplacerHelpTextsComponent::class, 'mailcoach::replacer-help-texts');

        Blade::component('mailcoach::app.components.dropdown', 'mailcoach::dropdown');

        Blade::component('mailcoach::app.layouts.app', 'mailcoach::layout');
        Blade::component('mailcoach::app.automations.layouts.automation', 'mailcoach::layout-automation');
        Blade::component('mailcoach::app.campaigns.layouts.campaign', 'mailcoach::layout-campaign');
        Blade::component('mailcoach::app.emailLists.layouts.emailList', 'mailcoach::layout-list');
        Blade::component('mailcoach::app.emailLists.segments.layouts.segment', 'mailcoach::layout-segment');
        Blade::component('mailcoach::app.emailLists.subscribers.layouts.subscriber', 'mailcoach::layout-subscriber');
        Blade::component('mailcoach::app.transactionalMails.layouts.transactional', 'mailcoach::layout-transactional');
        Blade::component('mailcoach::app.transactionalMails.templates.layouts.template', 'mailcoach::layout-transactional-template');
        Blade::component('mailcoach::app.automations.mails.layouts.automationMail', 'mailcoach::layout-automation-mail');

        Blade::component('mailcoach::app.automations.components.automationAction', 'mailcoach::automation-action');

        Blade::component('mailcoach::auth.layouts.auth', 'mailcoach::layout-auth');
        Blade::component('mailcoach::app.layouts.settings', 'mailcoach::layout-settings');

        Blade::component('mailcoach::app.components.codeCopy', 'mailcoach::code-copy');

        Blade::component('mailcoach::emailListWebsite.layouts.emailListWebsite', 'mailcoach::layout-website');

        return $this;
    }

    protected function bootLivewireComponents(): static
    {
        Livewire::addPersistentMiddleware([
            SetMailcoachDefaults::class,
        ]);

        Livewire::component('mailcoach::text-area-editor', TextAreaEditorComponent::class);

        Livewire::component('mailcoach::automation-builder', AutomationBuilder::class);

        Livewire::component('mailcoach::automation-action', AutomationActionComponent::class);
        Livewire::component('mailcoach::automation-mail-action', AutomationMailActionComponent::class);
        Livewire::component('mailcoach::add-tags-action', AddTagsActionComponent::class);
        Livewire::component('mailcoach::remove-tags-action', RemoveTagsActionComponent::class);
        Livewire::component('mailcoach::wait-action', WaitActionComponent::class);
        Livewire::component('mailcoach::condition-action', ConditionActionComponent::class);
        Livewire::component('mailcoach::split-action', SplitActionComponent::class);

        Livewire::component('mailcoach::date-trigger', DateTriggerComponent::class);
        Livewire::component('mailcoach::tag-added-trigger', TagAddedTriggerComponent::class);
        Livewire::component('mailcoach::tag-removed-trigger', TagRemovedTriggerComponent::class);
        Livewire::component('mailcoach::webhook-trigger', WebhookTriggerComponent::class);
        Livewire::component('mailcoach::no-trigger', NoTriggerComponent::class);

        Livewire::component('mailcoach::email-list-statistics', EmailListStatistics::class);
        Livewire::component('mailcoach::campaign-statistics', CampaignStatisticsComponent::class);

        Livewire::component('mailcoach::send-test', SendTestComponent::class);
        Livewire::component('mailcoach::data-table', DataTableComponent::class);

        Livewire::component('mailcoach::dashboard', Mailcoach::getLivewireClass('dashboard', DashboardComponent::class));
        Livewire::component('mailcoach::dashboard-chart', DashboardChart::class);

        // Audience
        Livewire::component('mailcoach::create-list', Mailcoach::getLivewireClass('create-list', CreateListComponent::class));
        Livewire::component('mailcoach::lists', Mailcoach::getLivewireClass('lists', ListsComponent::class));
        Livewire::component('mailcoach::list-summary', Mailcoach::getLivewireClass('list-summary', ListSummaryComponent::class));
        Livewire::component('mailcoach::list-settings', Mailcoach::getLivewireClass('list-settings', ListSettingsComponent::class));
        Livewire::component('mailcoach::list-onboarding', Mailcoach::getLivewireClass('list-onboarding', ListOnboardingComponent::class));
        Livewire::component('mailcoach::list-mailers', Mailcoach::getLivewireClass('list-mailers', ListMailersComponent::class));
        Livewire::component('mailcoach::list-mailers', Mailcoach::getLivewireClass('list-mailers', ListMailersComponent::class));
        Livewire::component('mailcoach::website', Mailcoach::getLivewireClass('website', WebsiteComponent::class));
        Livewire::component('mailcoach::create-segment', Mailcoach::getLivewireClass('create-segment', CreateSegmentComponent::class));
        Livewire::component('mailcoach::segments', Mailcoach::getLivewireClass('segments', SegmentsComponent::class));
        Livewire::component('mailcoach::segment', Mailcoach::getLivewireClass('segment', SegmentComponent::class));
        Livewire::component('mailcoach::segment-subscribers', Mailcoach::getLivewireClass('segment-subscribers', SegmentSubscribersComponent::class));
        Livewire::component('mailcoach::create-subscriber', Mailcoach::getLivewireClass('create-subscriber', CreateSubscriberComponent::class));
        Livewire::component('mailcoach::subscribers', Mailcoach::getLivewireClass('subscribers', SubscribersComponent::class));
        Livewire::component('mailcoach::subscriber', Mailcoach::getLivewireClass('subscriber', SubscriberComponent::class));
        Livewire::component('mailcoach::subscriber-sends', Mailcoach::getLivewireClass('subscriber-sends', SubscriberSendsComponent::class));
        Livewire::component('mailcoach::subscriber-imports', Mailcoach::getLivewireClass('subscriber-imports', SubscriberImportsComponent::class));
        Livewire::component('mailcoach::create-tag', Mailcoach::getLivewireClass('create-tag', CreateTagComponent::class));
        Livewire::component('mailcoach::tags', Mailcoach::getLivewireClass('tags', TagsComponent::class));
        Livewire::component('mailcoach::tag', Mailcoach::getLivewireClass('tag', TagComponent::class));

        // Automations
        Livewire::component('mailcoach::create-automation', Mailcoach::getLivewireClass('create-automation', CreateAutomationComponent::class));
        Livewire::component('mailcoach::automations', Mailcoach::getLivewireClass('automations', AutomationsComponent::class));
        Livewire::component('mailcoach::automation-settings', Mailcoach::getLivewireClass('automation-settings', AutomationSettingsComponent::class));
        Livewire::component('mailcoach::automation-actions', Mailcoach::getLivewireClass('automation-actions', AutomationActionsComponent::class));
        Livewire::component('mailcoach::automation-run', Mailcoach::getLivewireClass('automation-run', RunAutomationComponent::class));
        Livewire::component('mailcoach::create-automation-mail', Mailcoach::getLivewireClass('create-automation-mail', CreateAutomationMailComponent::class));
        Livewire::component('mailcoach::automation-mails', Mailcoach::getLivewireClass('automation-mails', AutomationMailsComponent::class));
        Livewire::component('mailcoach::automation-mail-summary', Mailcoach::getLivewireClass('automation-mail-summary', AutomationMailSummaryComponent::class));
        Livewire::component('mailcoach::automation-mail-settings', Mailcoach::getLivewireClass('automation-mail-settings', AutomationMailSettingsComponent::class));
        Livewire::component('mailcoach::automation-mail-clicks', Mailcoach::getLivewireClass('automation-mail-clicks', AutomationMailClicksComponent::class));
        Livewire::component('mailcoach::automation-mail-opens', Mailcoach::getLivewireClass('automation-mail-opens', AutomationMailOpensComponent::class));
        Livewire::component('mailcoach::automation-mail-unsubscribes', Mailcoach::getLivewireClass('automation-mail-unsubscribes', AutomationMailUnsubscribesComponent::class));
        Livewire::component('mailcoach::automation-mail-outbox', Mailcoach::getLivewireClass('automation-mail-outbox', AutomationMailOutboxComponent::class));

        // Campaigns
        Livewire::component('mailcoach::create-campaign', Mailcoach::getLivewireClass('create-campaign', CreateCampaignComponent::class));
        Livewire::component('mailcoach::campaigns', Mailcoach::getLivewireClass('campaigns', CampaignsComponent::class));
        Livewire::component('mailcoach::create-template', Mailcoach::getLivewireClass('create-template', CreateTemplateComponent::class));
        Livewire::component('mailcoach::templates', Mailcoach::getLivewireClass('templates', TemplatesComponent::class));
        Livewire::component('mailcoach::template', Mailcoach::getLivewireClass('template', TemplateComponent::class));
        Livewire::component('mailcoach::campaign-settings', Mailcoach::getLivewireClass('campaign-settings', CampaignSettingsComponent::class));
        Livewire::component('mailcoach::campaign-delivery', Mailcoach::getLivewireClass('campaign-delivery', CampaignDeliveryComponent::class));
        Livewire::component('mailcoach::campaign-summary', Mailcoach::getLivewireClass('campaign-summary', CampaignSummaryComponent::class));
        Livewire::component('mailcoach::campaign-clicks', Mailcoach::getLivewireClass('campaign-clicks', CampaignClicksComponent::class));
        Livewire::component('mailcoach::campaign-opens', Mailcoach::getLivewireClass('campaign-opens', CampaignOpensComponent::class));
        Livewire::component('mailcoach::campaign-unsubscribes', Mailcoach::getLivewireClass('campaign-unsubscribes', CampaignUnsubscribesComponent::class));
        Livewire::component('mailcoach::campaign-outbox', Mailcoach::getLivewireClass('campaign-outbox', CampaignOutboxComponent::class));

        // TransactionalMails
        Livewire::component('mailcoach::create-transactional-template', Mailcoach::getLivewireClass('create-transactional-template', CreateTransactionalTemplateComponent::class));
        Livewire::component('mailcoach::transactional-mails', Mailcoach::getLivewireClass('transactional-mails', TransactionalMailLogItemsComponent::class));
        Livewire::component('mailcoach::transactional-mail-templates', Mailcoach::getLivewireClass('transactional-mail-templates', TransactionalMailsComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-content', Mailcoach::getLivewireClass('transactional-mail-template-content', TransactionalTemplateContentComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-settings', Mailcoach::getLivewireClass('transactional-mail-template-settings', TransactionalTemplateSettingsComponent::class));
        Livewire::component('mailcoach::transactional-mail-content', Mailcoach::getLivewireClass('transactional-mail-content', TransactionalMailContentComponent::class));
        Livewire::component('mailcoach::transactional-mail-performance', Mailcoach::getLivewireClass('transactional-mail-performance', TransactionalMailPerformanceComponent::class));
        Livewire::component('mailcoach::transactional-mail-resend', Mailcoach::getLivewireClass('transactional-mail-resend', TransactionalMailResendComponent::class));

        Livewire::component('mailcoach::export', ExportComponent::class);
        Livewire::component('mailcoach::import', ImportComponent::class);

        // settings
        Livewire::component('mailcoach::webhooks', WebhooksComponent::class);
        Livewire::component('mailcoach::create-webhook', CreateWebhookComponent::class);
        Livewire::component('mailcoach::edit-webhook', EditWebhookComponent::class);

        Livewire::component('mailcoach::mailers', Mailcoach::getLivewireClass('mailers', MailersComponent::class));
        Livewire::component('mailcoach::create-mailer', Mailcoach::getLivewireClass('create-mailer', CreateMailerComponent::class));
        Livewire::component('mailcoach::create-user', Mailcoach::getLivewireClass('create-user', CreateUserComponent::class));
        Livewire::component('mailcoach::general-settings', Mailcoach::getLivewireClass('general-settings', GeneralSettingsComponent::class));
        Livewire::component('mailcoach::editor-settings', Mailcoach::getLivewireClass('editor-settings', EditorSettingsComponent::class));
        Livewire::component('mailcoach::mailer-send-test', Mailcoach::getLivewireClass('mailer-send-test', Http\Livewire\MailConfiguration\SendTestComponent::class));
        Livewire::component('mailcoach::profile', Mailcoach::getLivewireClass('profile', ProfileComponent::class));
        Livewire::component('mailcoach::password', Mailcoach::getLivewireClass('password', PasswordComponent::class));
        Livewire::component('mailcoach::users', Mailcoach::getLivewireClass('users', UsersComponent::class));
        Livewire::component('mailcoach::edit-user', Mailcoach::getLivewireClass('edit-user', EditUserComponent::class));
        Livewire::component('mailcoach::edit-mailer', Mailcoach::getLivewireClass('edit-mailer', EditMailerComponent::class));
        Livewire::component('mailcoach::tokens', Mailcoach::getLivewireClass('tokens', TokensComponent::class));

        SesSetupWizardComponent::registerLivewireComponents();
        SendGridSetupWizardComponent::registerLivewireComponents();
        SendinblueSetupWizardComponent::registerLivewireComponents();
        SmtpSetupWizardComponent::registerLivewireComponents();
        PostmarkSetupWizardComponent::registerLivewireComponents();
        MailgunSetupWizardComponent::registerLivewireComponents();

        return $this;
    }

    protected function bootEvents()
    {
        Event::listen(CampaignSentEvent::class, SendCampaignSentEmail::class);
        Event::listen(WebhookCallProcessedEvent::class, SetWebhookCallProcessedAt::class);
        Event::listen(MessageSending::class, StoreTransactionalMail::class);
        Event::listen(CampaignOpenedEvent::class, AddCampaignOpenedTag::class);
        Event::listen(CampaignLinkClickedEvent::class, AddCampaignClickedTag::class);
        Event::listen(AutomationMailOpenedEvent::class, AddAutomationMailOpenedTag::class);
        Event::listen(AutomationMailLinkClickedEvent::class, AddAutomationMailClickedTag::class);

        Event::subscribe(WebhookEventSubscriber::class);

        return $this;
    }

    protected function bootTriggers(): static
    {
        try {
            $triggers = cache()->rememberForever('automation-triggers', function () {
                return static::getAutomationTriggerClass()::with(['automation'])->has('automation')->get();
            });

            $triggers
                ->filter(fn (Trigger $trigger) => $trigger->trigger instanceof TriggeredByEvents)
                ->each(function (Trigger $trigger) {
                    if ($trigger->automation) {
                        Event::subscribe($trigger->trigger->setAutomation($trigger->automation));
                    }
                });
        } catch (Exception) {
            // Do nothing as the database is probably not set up yet.
        }

        return $this;
    }

    protected function registerApiGuard(): static
    {
        if (config('auth.guards.api')) {
            return $this;
        }

        config()->set('auth.guards.api', [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ]);

        return $this;
    }

    protected function bootSpotlight(): static
    {
        // Index commands
        Spotlight::registerCommand(AutomationsCommand::class);
        Spotlight::registerCommand(AutomationEmailsCommand::class);
        Spotlight::registerCommand(CampaignsCommand::class);
        Spotlight::registerCommand(HomeCommand::class);
        Spotlight::registerCommand(ListsCommand::class);
        Spotlight::registerCommand(TemplatesCommand::class);
        Spotlight::registerCommand(TransactionalLogCommand::class);
        Spotlight::registerCommand(TransactionalTemplatesCommand::class);

        // Show commands
        Spotlight::registerCommand(ShowAutomationCommand::class);
        Spotlight::registerCommand(ShowAutomationMailCommand::class);
        Spotlight::registerCommand(ShowCampaignCommand::class);
        Spotlight::registerCommand(ShowListCommand::class);
        Spotlight::registerCommand(ShowTemplateCommand::class);
        Spotlight::registerCommand(ShowTransactionalTemplateCommand::class);

        // Create commands
        Spotlight::registerCommand(CreateAutomationCommand::class);
        Spotlight::registerCommand(CreateAutomationMailCommand::class);
        Spotlight::registerCommand(CreateCampaignCommand::class);
        Spotlight::registerCommand(CreateListCommand::class);
        Spotlight::registerCommand(CreateTemplateCommand::class);
        Spotlight::registerCommand(CreateTransactionalTemplateCommand::class);

        config()->set('livewire-ui-spotlight.show_results_without_input', true);
        config()->set('livewire-ui-spotlight.shortcuts', ['slash']);
        config()->set('livewire-ui-spotlight.include_js', false);
        config()->set('livewire-ui-spotlight.include_css', false);

        return $this;
    }

    protected function bootEncryption(): static
    {
        if (! config('mailcoach.encryption.enabled')) {
            return $this;
        }

        $encryptionKey = config('mailcoach.encryption.key');

        if (Str::startsWith($encryptionKey, $prefix = 'base64:')) {
            $encryptionKey = base64_decode(Str::after($encryptionKey, $prefix));
        }

        config()->set('ciphersweet.providers.string.key', $encryptionKey);

        return $this;
    }
}
