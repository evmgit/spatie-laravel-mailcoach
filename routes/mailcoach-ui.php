<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\UploadsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\SendTransactionalMailTestController;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplateComponent;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplatesComponent;
use Spatie\Mailcoach\Http\App\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Http\App\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapSettingsNavigation;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;
use Spatie\Mailcoach\Http\Livewire\EditMailerComponent;
use Spatie\Mailcoach\Http\Livewire\EditorSettingsComponent;
use Spatie\Mailcoach\Http\Livewire\EditUserComponent;
use Spatie\Mailcoach\Http\Livewire\EditWebhookComponent;
use Spatie\Mailcoach\Http\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Http\Livewire\MailersComponent;
use Spatie\Mailcoach\Http\Livewire\PasswordComponent;
use Spatie\Mailcoach\Http\Livewire\ProfileComponent;
use Spatie\Mailcoach\Http\Livewire\TokensComponent;
use Spatie\Mailcoach\Http\Livewire\UsersComponent;
use Spatie\Mailcoach\Http\Livewire\WebhooksComponent;
use Spatie\Mailcoach\Mailcoach;

Route::get('dashboard', Mailcoach::getLivewireClass('dashboard', \Spatie\Mailcoach\Http\App\Livewire\DashboardComponent::class))->name('mailcoach.dashboard');
Route::get('debug', '\\'.DebugController::class)->name('debug');

Route::post('uploads', UploadsController::class);

Route::get('export', '\\'.ExportComponent::class)->name('export');
Route::get('import', '\\'.ImportComponent::class)->name('import');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('campaigns', Mailcoach::getLivewireClass('campaigns', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignsComponent::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass('campaign-settings', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettingsComponent::class))->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\'.CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', Mailcoach::getLivewireClass('campaign-delivery', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDeliveryComponent::class))->name('mailcoach.campaigns.delivery');

        Route::middleware('\\'.EditableCampaign::class)->group(function () {
            Route::put('content', ['\\'.CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');
        });

        Route::get('summary', '\\'.Mailcoach::getLivewireClass('campaign-summary', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummaryComponent::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\'.Mailcoach::getLivewireClass('campaign-opens', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpensComponent::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('campaign-links', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicksComponent::class))->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('campaign-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribesComponent::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('campaign-outbox', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutboxComponent::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('lists', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListsComponent::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('list-summary', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummaryComponent::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscribersComponent::class))->name('mailcoach.emailLists.subscribers');
            Route::post('export', '\\'.SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');
            Route::get('{subscriber}', '\\'.Mailcoach::getLivewireClass('subscriber', \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberComponent::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', '\\'.Mailcoach::getLivewireClass('subscriber-imports', \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberImportsComponent::class))->name('mailcoach.emailLists.import-subscribers');

        Route::get('settings', '\\'.Mailcoach::getLivewireClass('list-settings', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettingsComponent::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\'.Mailcoach::getLivewireClass('list-onboarding', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboardingComponent::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\'.Mailcoach::getLivewireClass('list-mailers', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailersComponent::class))->name('mailcoach.emailLists.mailers');
        Route::get('website', '\\'.Mailcoach::getLivewireClass('list-website', \Spatie\Mailcoach\Http\App\Livewire\Audience\WebsiteComponent::class))->name('mailcoach.emailLists.website');

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('tags', \Spatie\Mailcoach\Http\App\Livewire\Audience\TagsComponent::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag}', '\\'.Mailcoach::getLivewireClass('tag', \Spatie\Mailcoach\Http\App\Livewire\Audience\TagComponent::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('segments', \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentsComponent::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment}', '\\'.Mailcoach::getLivewireClass('segment', \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentComponent::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automations', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationsComponent::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettingsComponent::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\'.Mailcoach::getLivewireClass('automation-run', \Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomationComponent::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\'.Mailcoach::getLivewireClass('automation-actions', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActionsComponent::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automation-mails', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailsComponent::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('automation-mail-summary', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummaryComponent::class))->name('mailcoach.automations.mails.summary');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-mail-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettingsComponent::class))->name('mailcoach.automations.mails.settings');
        Route::get('delivery', '\\'.Mailcoach::getLivewireClass('automation-mail-delivery', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailDeliveryComponent::class))->name('mailcoach.automations.mails.delivery');

        Route::get('opens', '\\'.Mailcoach::getLivewireClass('automation-mail-opens', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpensComponent::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('automation-mail-clicks', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicksComponent::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribesComponent::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('automation-mail-outbox', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutboxComponent::class))->name('mailcoach.automations.mails.outbox');

        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mails', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailLogItemsComponent::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContentComponent::class))->name('mailcoach.transactionalMails.show');
        Route::get('performance', '\\'.Mailcoach::getLivewireClass('transactional-mail-performance', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformanceComponent::class))->name('mailcoach.transactionalMails.performance');
        Route::get('resend', '\\'.Mailcoach::getLivewireClass('transactional-mail-resend', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResendComponent::class))->name('mailcoach.transactionalMails.resend');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mail-templates', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailsComponent::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContentComponent::class))->name('mailcoach.transactionalMails.templates.edit');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-settings', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent::class))->name('mailcoach.transactionalMails.templates.settings');

        Route::post('send-test-email', '\\'.SendTransactionalMailTestController::class)->name('mailcoach.transactionalMails.templates.sendTestEmail');
    });
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('templates', TemplatesComponent::class))->name('mailcoach.templates');
    Route::get('{template}', '\\'.Mailcoach::getLivewireClass('template', TemplateComponent::class))->name('mailcoach.templates.edit');
});

Route::prefix('settings')
    ->middleware([BootstrapSettingsNavigation::class])
    ->group(function () {
        Route::get('general', GeneralSettingsComponent::class)->name('general-settings');

        Route::prefix('account')->group(function () {
            Route::get('details', ProfileComponent::class)->name('account');

            Route::get('password', PasswordComponent::class)->name('password');

            Route::prefix('tokens')->group(function () {
                Route::get('/', TokensComponent::class)->name('tokens');
            });
        });

        Route::prefix('mailers')->group(function () {
            Route::get('/', MailersComponent::class)->name('mailers');
            Route::get('{mailer}', EditMailerComponent::class)->name('mailers.edit');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', UsersComponent::class)->name('users');
            Route::get('{user}', EditUserComponent::class)->name('users.edit');
        });

        Route::get('editor', EditorSettingsComponent::class)->name('editor');

        Route::prefix('webhooks')->group(function () {
            Route::get('/', WebhooksComponent::class)->name('webhooks');
            Route::get('{webhook}', EditWebhookComponent::class)->name('webhooks.edit');
        });
    });
