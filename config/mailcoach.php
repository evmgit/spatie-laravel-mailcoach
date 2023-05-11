<?php

return [
    'campaigns' => [
        /*
         * The default mailer used by Mailcoach for sending campaigns.
         */
        'mailer' => null,

        /*
         * Replacers are classes that can make replacements in the html of a campaign.
         *
         * You can use a replacer to create placeholders.
         */
        'replacers' => [
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebsiteUrlCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebsiteCampaignUrlCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\WebviewCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\SubscriberReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\EmailListCampaignReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\UnsubscribeUrlReplacer::class,
            \Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignNameCampaignReplacer::class,
        ],

        /*
         * Here you can specify which jobs should run on which queues.
         * Use an empty string to use the default queue.
         */
        'perform_on_queue' => [
            'send_campaign_job' => 'send-campaign',
            'send_mail_job' => 'send-mail',
            'send_test_mail_job' => 'mailcoach',
            'process_feedback_job' => 'mailcoach-feedback',
            'import_subscribers_job' => 'mailcoach',
        ],

        /*
         * The job that will send a campaign could take a long time when your list contains a lot of subscribers.
         * Here you can define the maximum run time of the job. If the job hasn't fully sent your campaign, it
         * will redispatch itself.
         */
        'send_campaign_maximum_job_runtime_in_seconds' => 60 * 10,

        /*
         * You can customize some of the behavior of this package by using our own custom action.
         * Your custom action should always extend the one of the default ones.
         */
        'actions' => [
            'prepare_email_html' => \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction::class,
            'prepare_subject' => \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction::class,
            'prepare_webview_html' => \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction::class,
            'convert_html_to_text' => \Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction::class,
            'personalize_html' => \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction::class,
            'personalize_subject' => \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction::class,
            'retry_sending_failed_sends' => \Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction::class,
            'send_campaign' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction::class,
            'send_campaign_mails' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction::class,
            'send_mail' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction::class,
            'send_test_mail' => \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignTestAction::class,
            'validate_campaign_requirements' => \Spatie\Mailcoach\Domain\Campaign\Actions\ValidateCampaignRequirementsAction::class,
        ],

        /*
         * Adapt these settings if you prefer other default settings for newly created campaigns
         */
        'default_settings' => [
            'utm_tags' => true,
        ],

        /**
         * Here you can configure which fields of the campaigns you want to search in
         * from the Campaigns section in the view. The value is an array of fields.
         * For relations fields, you can use the dot notation (e.g. 'emailList.name').
         */
        'search_fields' => ['name'],
    ],

    'automation' => [
        /*
         * The default mailer used by Mailcoach for automation mails.
         */
        'mailer' => null,

        /*
         * The job that will send automation mails could take a long time when your list contains a lot of subscribers.
         * Here you can define the maximum run time of the job. If the job hasn't fully sent your automation mails, it
         * will redispatch itself.
         */
        'send_automation_mails_maximum_job_runtime_in_seconds' => 60 * 10,

        'actions' => [
            'send_mail' => \Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction::class,
            'send_automation_mail_to_subscriber' => \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction::class,
            'send_automation_mails_action' => \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction::class,
            'prepare_subject' => \Spatie\Mailcoach\Domain\Automation\Actions\PrepareSubjectAction::class,
            'prepare_webview_html' => \Spatie\Mailcoach\Domain\Automation\Actions\PrepareWebviewHtmlAction::class,

            'convert_html_to_text' => \Spatie\Mailcoach\Domain\Automation\Actions\ConvertHtmlToTextAction::class,
            'prepare_email_html' => \Spatie\Mailcoach\Domain\Automation\Actions\PrepareEmailHtmlAction::class,
            'personalize_html' => \Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeHtmlAction::class,
            'personalize_subject' => \Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeSubjectAction::class,
            'send_test_mail' => \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction::class,

            'should_run_for_subscriber' => \Spatie\Mailcoach\Domain\Automation\Actions\ShouldAutomationRunForSubscriberAction::class,
        ],

        'replacers' => [
            \Spatie\Mailcoach\Domain\Automation\Support\Replacers\WebviewAutomationMailReplacer::class,
            \Spatie\Mailcoach\Domain\Automation\Support\Replacers\SubscriberReplacer::class,
            \Spatie\Mailcoach\Domain\Automation\Support\Replacers\UnsubscribeUrlReplacer::class,
            \Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailNameAutomationMailReplacer::class,
        ],

        'flows' => [
            /**
             * The available actions in the automation flows. You can add custom
             * actions to this array, make sure they extend
             * \Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction
             */
            'actions' => [
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\AddTagsAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction::class,
            ],

            /**
             * The available triggers in the automation settings. You can add
             * custom triggers to this array, make sure they extend
             * \Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger
             */
            'triggers' => [
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\NoTrigger::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagAddedTrigger::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagRemovedTrigger::class,
                \Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger::class,
            ],

            /**
             * Custom conditions for the ConditionAction, these have to implement the
             * \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition
             * interface.
             */
            'conditions' => [],
        ],

        'perform_on_queue' => [
            'dispatch_pending_automation_mails_job' => 'send-campaign',
            'run_automation_action_job' => 'send-campaign',
            'run_action_for_subscriber_job' => 'mailcoach',
            'run_automation_for_subscriber_job' => 'mailcoach',
            'send_automation_mail_to_subscriber_job' => 'send-automation-mail',
            'send_automation_mail_job' => 'send-mail',
            'send_test_mail_job' => 'mailcoach',
        ],

        /*
         * Adapt these settings if you prefer other default settings for newly created campaigns
         */
        'default_settings' => [
            'utm_tags' => true,
        ],
    ],

    'audience' => [
        'actions' => [
            'confirm_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction::class,
            'create_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction::class,
            'delete_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction::class,
            'import_subscribers' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscribersAction::class,
            'import_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction::class,
            'send_confirm_subscriber_mail' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction::class,
            'update_subscriber' => \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction::class,
        ],

        /*
         * This disk will be used to store files regarding importing subscribers.
         */
        'import_subscribers_disk' => 'local',
    ],

    'transactional' => [
        /*
         * The default mailer used by Mailcoach for transactional mails.
         */
        'mailer' => null,

        /*
         * Replacers are classes that can make replacements in the body of transactional mails.
         *
         * You can use replacers to create placeholders.
         */
        'replacers' => [
            'subject' => \Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\SubjectReplacer::class,
        ],

        'actions' => [
            'send_test' => \Spatie\Mailcoach\Domain\TransactionalMail\Actions\SendTestForTransactionalMailTemplateAction::class,
            'render_template' => \Spatie\Mailcoach\Domain\TransactionalMail\Actions\RenderTemplateAction::class,
        ],

        /**
         * Here you can configure which fields of the transactional mails you want to search in
         * from the Transactional Log section in the view. The value is an array of fields.
         * For relations fields, you can use the dot notation.
         */
        'search_fields' => ['subject'],
    ],

    'shared' => [
        /*
         * Here you can specify which jobs should run on which queues.
         * Use an empty string to use the default queue.
         */
        'perform_on_queue' => [
            'schedule' => 'mailcoach-schedule',
            'calculate_statistics_job' => 'mailcoach',
            'send_webhooks' => 'mailcoach',
        ],

        'actions' => [
            'calculate_statistics' => \Spatie\Mailcoach\Domain\Shared\Actions\CalculateStatisticsAction::class,
            'send_webhook' => \Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction::class,
        ],
    ],

    /**
     * Whether Mailcoach should encrypt personal information.
     * This will encrypt the email address, first_name,
     * last_name and extra attributes of subscribers.
     */
    'encryption' => [
        'enabled' => false,
        'key' => env('MAILCOACH_ENCRYPTION_KEY', env('APP_KEY')),
    ],

    /*
     * Here you can configure which content editor Mailcoach uses.
     * By default this is a text editor that highlights HTML.
     */
    'content_editor' => \Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent::class,

    /*
     * Here you can configure which template editor Mailcoach uses.
     * By default this is a text editor that highlights HTML.
     */
    'template_editor' => \Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent::class,

    /*
     * This disk will be used to store files regarding importing.
     */
    'import_disk' => 'local',

    /*
     * This disk will be used to store files regarding exporting.
     */
    'export_disk' => 'local',

    /*
     * This disk will be used to store assets for the public archive
     * of an email list. You should make sure that this disk is
     * publicly reachable.
     */
    'website_disk' => 'public',

    /*
     * We will put all mailcoach files in this directory
     * on the disk.
     */
    'website_disk_directory' => 'mailcoach-files',

    /*
     * This disk will be used to store files temporarily for
     * unzipping & reading. Make sure this is on a local
     * filesystem.
     */
    'tmp_disk' => 'local',

    /*
     * The mailer used by Mailcoach for password resets and summary emails.
     * Mailcoach will use the default Laravel mailer if this is not set.
     */
    'mailer' => null,

    /*
     * The timezone to use with Mailcoach, by default the timezone in
     * config/app.php will be used.
     */
    'timezone' => null,

    /*
     * The date format used on all screens of the UI
     */
    'date_format' => 'Y-m-d H:i',

    /*
     * Here you can specify on which connection Mailcoach's jobs will be dispatched.
     * Leave empty to use the app default's env('QUEUE_CONNECTION')
     */
    'queue_connection' => '',

    /*
     * Unauthorized users will get redirected to this route.
     */
    'redirect_unauthorized_users_to_route' => 'mailcoach.login',

    /*
     * Homepage will redirect to this route.
     */
    'redirect_home' => 'mailcoach.dashboard',

    /*
     *  This configuration option defines the authentication guard that will
     *  be used to protect your the Mailcoach UI. This option should match one
     *  of the authentication guards defined in the "auth" config file.
     */
    'guard' => env('MAILCOACH_GUARD', null),

    /*
     *  These middleware will be assigned to every Mailcoach routes, giving you the chance
     *  to add your own middleware to this stack or override any of the existing middleware.
     */
    'middleware' => [
        'web' => [
            'web',
            Spatie\Mailcoach\Http\App\Middleware\Authenticate::class,
            Spatie\Mailcoach\Http\App\Middleware\Authorize::class,
            Spatie\Mailcoach\Http\App\Middleware\SetMailcoachDefaults::class,
            Spatie\Mailcoach\Http\App\Middleware\BootstrapNavigation::class,
        ],
        'api' => [
            'api',
            'auth:sanctum',
        ],
    ],

    'uploads' => [
        /*
         * The disk on which to store uploaded images from the editor. Choose
         * one or more of the disks you've configured in config/filesystems.php.
         */
        'disk_name' => env('MEDIA_DISK', 'public'),

        /*
         * The media collection name to use when storing uploaded images from the editor.
         * You probably don't need to change this,
         * unless you're already using spatie/laravel-medialibrary in your project.
         */
        'collection_name' => env('MEDIA_COLLECTION', 'default'),

        /**
         * The max width that will be set for the uploaded conversion
         */
        'max_width' => 1500,

        /**
         * The max height that will be set for the uploaded conversion
         */
        'max_height' => 1500,
    ],

    'models' => [
        /*
         * The model you want to use as a Campaign model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class`
         * model.
         */
        'campaign' => Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class,

        /*
         * The model you want to use as a CampaignLink model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink::class`
         * model.
         */
        'campaign_link' => \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink::class,

        /*
         * The model you want to use as a CampaignClick model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick::class`
         * model.
         */
        'campaign_click' => \Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick::class,

        /*
         * The model you want to use as a CampaignOpen model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen::class`
         * model.
         */
        'campaign_open' => \Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen::class,

        /*
         * The model you want to use as a CampaignUnsubscribe model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe::class`
         * model.
         */
        'campaign_unsubscribe' => \Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe::class,

        /*
         * The model you want to use as a EmailList model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Audience\Models\EmailList::class`
         * model.
         */
        'email_list' => \Spatie\Mailcoach\Domain\Audience\Models\EmailList::class,

        /*
         * The model you want to use as a Send model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Shared\Models\Send::class`
         * model.
         */
        'send' => \Spatie\Mailcoach\Domain\Shared\Models\Send::class,

        /*
         * The model you want to use as a SendFeedbackItem model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem::class`
         * model.
         */
        'send_feedback_item' => \Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem::class,

        /*
         * The model you want to use as a Subscriber model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Audience\Models\Subscriber::class`
         * model.
         */
        'subscriber' => \Spatie\Mailcoach\Domain\Audience\Models\Subscriber::class,

        /*
         * The model you want to use as a SubscriberImport model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport::class`
         * model.
         */
        'subscriber_import' => \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport::class,

        /*
         * The model you want to use as a Tag model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Audience\Models\Tag::class`
         * model.
         */
        'tag' => Spatie\Mailcoach\Domain\Audience\Models\Tag::class,

        /*
         * The model you want to use as a TagSegment model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Audience\Models\TagSegment::class`
         * model.
         */
        'tag_segment' => Spatie\Mailcoach\Domain\Audience\Models\TagSegment::class,

        /*
         * The model you want to use as a Template model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Campaign\Models\Template::class`
         * model.
         */
        'template' => Spatie\Mailcoach\Domain\Campaign\Models\Template::class,

        /*
         * The model you want to use as a TransactionalMailLogItem model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem::class`
         * model.
         */
        'transactional_mail_log_item' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem::class,

        /*
         * The model you want to use as a TransactionalMailOpen model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen::class`
         * model.
         */
        'transactional_mail_open' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen::class,

        /*
         * The model you want to use as a TransactionalMailClick model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick::class`
         * model.
         */
        'transactional_mail_click' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick::class,

        /*
         * The model you want to use as a TransactionalMail model. It needs to be or
         * extend the `\Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail::class`
         * model.
         */
        'transactional_mail' => \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail::class,

        /*
         * The model you want to use as an Automation model. It needs to be or
         * extend the `\Spatie\Mailcoach\Domain\Automation\Models\Automation::class`
         * model.
         */
        'automation' => \Spatie\Mailcoach\Domain\Automation\Models\Automation::class,

        /*
         * The model you want to use as an Action model. It needs to be or
         * extend the `\Spatie\Mailcoach\Domain\Automation\Models\Action::class`
         * model.
         */
        'automation_action' => \Spatie\Mailcoach\Domain\Automation\Models\Action::class,

        /*
         * The model you want to use as a Trigger model. It needs to be or
         * extend the `\Spatie\Mailcoach\Domain\Automation\Models\Trigger::class`
         * model.
         */
        'automation_trigger' => \Spatie\Mailcoach\Domain\Automation\Models\Trigger::class,

        /*
         * The model you want to use as an Automation mail model. It needs to be or
         * extend the `\Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::class` model.
         */
        'automation_mail' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::class,

        /*
         * The model you want to use as a Campaign model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink::class`
         * model.
         */
        'automation_mail_link' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink::class,

        /*
         * The model you want to use as a Campaign model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick::class`
         * model.
         */
        'automation_mail_click' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick::class,

        /*
         * The model you want to use as a Campaign model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen::class`
         * model.
         */
        'automation_mail_open' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen::class,

        /*
         * The model you want to use as a Campaign model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Automation\Models\AutomationMailUnsubscribe::class`
         * model.
         */
        'automation_mail_unsubscribe' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailUnsubscribe::class,

        /*
         * The model you want to use as the pivot between an Automation Action model
         * and the Subscriber model. It needs to be or extend the
         * `\Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber::class` model.
         */
        'action_subscriber' => \Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber::class,

        /*
         * The model you want to use as the Upload model. It needs to be or
         * extend the `Spatie\Mailcoach\Domain\Shared\Models\Upload::class`
         * model.
         */
        'upload' => \Spatie\Mailcoach\Domain\Shared\Models\Upload::class,

        'user' => \Spatie\Mailcoach\Domain\Settings\Models\User::class,
        'personal_access_token' => \Spatie\Mailcoach\Domain\Settings\Models\PersonalAccessToken::class,
        'setting' => \Spatie\Mailcoach\Domain\Settings\Models\Setting::class,
        'mailer' => \Spatie\Mailcoach\Domain\Settings\Models\Mailer::class,
        'webhook_configuration' => \Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration::class,
    ],

    'views' => [
        /*
         * The service provider registers several Blade components that are
         * used in Mailcoach's views. If you are using the default Mailcoach
         * views, leave this as true so they work as expected. If you have
         * your own views and don't need/want Mailcoach to register these
         * blade components (e.g., because of naming conflicts), you can
         * change this setting to false and they won't be registered.
         *
         * If you change this setting, be sure to run `php artisan view:clear`
         * so Laravel can recompile your views.
         */
        'use_blade_components' => true,
    ],

    'livewire' => [
        'dashboard' => \Spatie\Mailcoach\Http\App\Livewire\DashboardComponent::class,

        // Audience
        'create-list' => \Spatie\Mailcoach\Http\App\Livewire\Audience\CreateListComponent::class,
        'lists' => \Spatie\Mailcoach\Http\App\Livewire\Audience\ListsComponent::class,
        'list-summary' => \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummaryComponent::class,
        'list-settings' => \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettingsComponent::class,
        'list-onboarding' => \Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboardingComponent::class,
        'list-mailers' => \Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailersComponent::class,
        'website-settings' => \Spatie\Mailcoach\Http\App\Livewire\Audience\WebsiteComponent::class,

        'create-segment' => \Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSegmentComponent::class,
        'segments' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentsComponent::class,
        'segment' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentComponent::class,
        'segment-subscribers' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentSubscribersComponent::class,
        'create-subscriber' => \Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSubscriberComponent::class,
        'subscribers' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscribersComponent::class,
        'subscriber' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberComponent::class,
        'subscriber-imports' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberImportsComponent::class,
        'subscriber-sends' => \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberSendsComponent::class,
        'create-tag' => \Spatie\Mailcoach\Http\App\Livewire\Audience\CreateTagComponent::class,
        'tags' => \Spatie\Mailcoach\Http\App\Livewire\Audience\TagsComponent::class,
        'tag' => \Spatie\Mailcoach\Http\App\Livewire\Audience\TagComponent::class,

        // Automations
        'create-automation' => \Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomationComponent::class,
        'automations' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationsComponent::class,
        'automation-settings' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettingsComponent::class,
        'automation-actions' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActionsComponent::class,
        'automation-run' => \Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomationComponent::class,
        'create-automation-mail' => \Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomationMailComponent::class,
        'automation-mails' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailsComponent::class,
        'automation-mail-summary' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummaryComponent::class,
        'automation-mail-settings' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettingsComponent::class,
        'automation-mail-clicks' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicksComponent::class,
        'automation-mail-opens' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpensComponent::class,
        'automation-mail-unsubscribes' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribesComponent::class,
        'automation-mail-outbox' => \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutboxComponent::class,

        // Campaigns
        'create-campaign' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateCampaignComponent::class,
        'campaigns' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignsComponent::class,
        'create-template' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateTemplateComponent::class,
        'templates' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplatesComponent::class,
        'template' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplateComponent::class,
        'campaign-settings' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettingsComponent::class,
        'campaign-delivery' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDeliveryComponent::class,
        'campaign-summary' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummaryComponent::class,
        'campaign-clicks' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicksComponent::class,
        'campaign-opens' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpensComponent::class,
        'campaign-unsubscribes' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribesComponent::class,
        'campaign-outbox' => \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutboxComponent::class,

        // Transactional
        'create-transactional-template' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\CreateTransactionalTemplateComponent::class,
        'transactional-mails' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailLogItemsComponent::class,
        'transactional-mail-templates' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailsComponent::class,
        'transactional-mail-template-content' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContentComponent::class,
        'transactional-mail-template-settings' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent::class,
        'transactional-mail-content' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContentComponent::class,
        'transactional-mail-performance' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformanceComponent::class,
        'transactional-mail-resend' => \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResendComponent::class,
    ],

    /**
     * The available editors inside Mailcoach UI, the key is the displayed name in the UI
     * the class must be a class that extends and implements
     * \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver
     */
    'editors' => [
        \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorJsEditorConfigurationDriver::class,
        \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\MarkdownEditorConfigurationDriver::class,
        \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\MonacoEditorConfigurationDriver::class,
        \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\TextareaEditorConfigurationDriver::class,
        \Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\UnlayerEditorConfigurationDriver::class,
    ],
];
