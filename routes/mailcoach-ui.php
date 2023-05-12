<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationActionsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailClicksController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailOpensController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailOutboxController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSummaryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailUnsubscribesController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\CreateAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DestroyAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DuplicateAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\SendAutomationMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\CreateAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\DestroyAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\DuplicateAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\RunAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\ToggleAutomationStatusController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\CampaignsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\CancelSendingCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DestroyCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CreateCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\ScheduleCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignTestController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\UnscheduleCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\RetryFailedSendsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignClicksController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignOpensController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignOutboxController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignSummaryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignUnsubscribesController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\DestroyEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\CreateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DestroySegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DuplicateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\EditSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\SegmentsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\SegmentSubscribersIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListMailersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListOnboardingController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\CreateSubscriberController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroyAllUnsubscribedController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroySubscriberController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\ReceivedCampaignsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscriberDetailsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SummaryController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\ConfirmController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\ResubscribeController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\UnsubscribeController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\TagsController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DestroySubscriberImportController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DownloadSubscriberImportAttachmentController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\DestroyTransactionalMailController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\ShowTransactionalMailBodyController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\SendTransactionalMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\TransactionalMailSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\TransactionalMailTemplatesController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\TransactionalMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\TransactionalMailIndexController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\TransactionalMailPerformanceController;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;

Route::get('debug', '\\' . DebugController::class)->name('debug');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\' . CampaignsIndexController::class)->name('mailcoach.campaigns');
    Route::post('/', '\\' . CreateCampaignController::class)->name('mailcoach.campaigns.store');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', ['\\' . CampaignSettingsController::class, 'edit'])->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\' . CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', '\\' . CampaignDeliveryController::class)->name('mailcoach.campaigns.delivery');

        Route::middleware('\\' . EditableCampaign::class)->group(function () {
            Route::put('settings', ['\\' . CampaignSettingsController::class, 'update']);
            Route::put('content', ['\\' . CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');

            Route::post('send-test-email', '\\' . SendCampaignTestController::class)->name('mailcoach.campaigns.sendTestEmail');
            Route::post('schedule', '\\' . ScheduleCampaignController::class)->name('mailcoach.campaigns.schedule');
            Route::post('unschedule', '\\' . UnscheduleCampaignController::class)->name('mailcoach.campaigns.unschedule');
            Route::post('send', '\\' . SendCampaignController::class)->name('mailcoach.campaigns.send');
        });

        Route::get('summary', '\\' . CampaignSummaryController::class)->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\' . CampaignOpensController::class)->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\' . CampaignClicksController::class)->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\' . CampaignUnsubscribesController::class)->name('mailcoach.campaigns.unsubscribes');

        Route::get('outbox', '\\' . CampaignOutboxController::class)->name('mailcoach.campaigns.outbox');

        Route::delete('/', '\\' . DestroyCampaignController::class)->name('mailcoach.campaigns.delete');
        Route::post('duplicate', '\\' . DuplicateCampaignController::class)->name('mailcoach.campaigns.duplicate');
        Route::post('retry-failed-sends', '\\' . RetryFailedSendsController::class)->name('mailcoach.campaigns.retry-failed-sends');
        Route::post('cancel-sending', '\\' . CancelSendingCampaignController::class)->name('mailcoach.campaigns.cancel-sending');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\' . EmailListsIndexController::class)->name('mailcoach.emailLists');
    Route::post('/', '\\' . CreateEmailListController::class)->name('mailcoach.emailLists.store');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\' . SummaryController::class)->name('mailcoach.emailLists.summary');
        Route::get('subscribers', '\\' . SubscribersIndexController::class)->name('mailcoach.emailLists.subscribers');
        Route::post('subscribers/export', '\\' . SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');

        Route::post('subscriber/create', ['\\' . CreateSubscriberController::class, 'store'])->name('mailcoach.emailLists.subscriber.store');
        Route::prefix('subscriber/{subscriber}')->group(function () {
            Route::get('details', ['\\' . SubscriberDetailsController::class, 'edit'])->name('mailcoach.emailLists.subscriber.details');
            Route::put('details', ['\\' . SubscriberDetailsController::class, 'update']);
            Route::get('attributes', ['\\' . SubscriberDetailsController::class, 'attributes'])->name('mailcoach.emailLists.subscriber.attributes');
            Route::get('received-campaigns', '\\' . ReceivedCampaignsController::class)->name('mailcoach.emailLists.subscriber.receivedCampaigns');
            Route::delete('/', '\\' . DestroySubscriberController::class)->name('mailcoach.emailLists.subscriber.delete');
        });

        Route::delete('unsubscribes', '\\' . DestroyAllUnsubscribedController::class)->name('mailcoach.emailLists.destroy-unsubscribes');

        Route::get('subscribers/import-subscribers', ['\\' . ImportSubscribersController::class, 'showImportScreen'])->name('mailcoach.emailLists.import-subscribers');
        Route::post('subscribers/import-subscribers', ['\\' . ImportSubscribersController::class, 'import']);

        Route::get('general-settings', ['\\' . EmailListGeneralSettingsController::class, 'edit'])->name('mailcoach.emailLists.general-settings');
        Route::put('general-settings', ['\\' . EmailListGeneralSettingsController::class, 'update']);

        Route::get('onboarding', ['\\' . EmailListOnboardingController::class, 'edit'])->name('mailcoach.emailLists.onboarding');
        Route::put('onboarding', ['\\' . EmailListOnboardingController::class, 'update']);

        Route::get('mailers', ['\\' . EmailListMailersController::class, 'edit'])->name('mailcoach.emailLists.mailers');
        Route::put('mailers', ['\\' . EmailListMailersController::class, 'update']);

        Route::prefix('tags')->group(function () {
            Route::get('/', ['\\' . TagsController::class, 'index'])->name('mailcoach.emailLists.tags');
            Route::post('/', ['\\' . TagsController::class, 'store'])->name('mailcoach.emailLists.tag.store');
            Route::prefix('{tag}')->group(function () {
                Route::get('/', ['\\' . TagsController::class, 'edit'])->name('mailcoach.emailLists.tag.edit');
                Route::put('/', ['\\' . TagsController::class, 'update']);
                Route::delete('/', ['\\' . TagsController::class, 'destroy'])->name('mailcoach.emailLists.tag.delete');
            });
        });
        Route::delete('/', '\\' . DestroyEmailListController::class)->name('mailcoach.emailLists.delete');

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\' . SegmentsIndexController::class)->name('mailcoach.emailLists.segments');

            Route::post('/', '\\' . CreateSegmentController::class)->name('mailcoach.emailLists.segment.store');

            Route::prefix('{segment}')->group(function () {
                Route::get('subscribers', '\\' . SegmentSubscribersIndexController::class)->name('mailcoach.emailLists.segment.subscribers');
                Route::get('/details', ['\\' . EditSegmentController::class, 'edit'])->name('mailcoach.emailLists.segment.edit');
                Route::put('/details', ['\\' . EditSegmentController::class, 'update']);
                Route::delete('/', '\\' . DestroySegmentController::class)->name('mailcoach.emailLists.segment.delete');
                Route::post('duplicate', '\\' . DuplicateSegmentController::class)->name('mailcoach.emailLists.segment.duplicate');
            });
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\' . AutomationsIndexController::class)->name('mailcoach.automations');
    Route::post('/', '\\' . CreateAutomationController::class)->name('mailcoach.automations.store');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', ['\\' . AutomationSettingsController::class, 'edit'])->name('mailcoach.automations.settings');
        Route::put('settings', ['\\' . AutomationSettingsController::class, 'update']);
        Route::get('run', ['\\' . RunAutomationController::class, 'edit'])->name('mailcoach.automations.run');
        Route::put('run', ['\\' . RunAutomationController::class, 'update']);

        Route::delete('/', '\\' . DestroyAutomationController::class)->name('mailcoach.automations.delete');
        Route::post('duplicate', '\\' . DuplicateAutomationController::class)->name('mailcoach.automations.duplicate');
        Route::post('toggle-status', '\\' . ToggleAutomationStatusController::class)->name('mailcoach.automations.toggleStatus');

        Route::prefix('actions')->group(function () {
            Route::get('/', ['\\' . AutomationActionsController::class, 'index'])->name('mailcoach.automations.actions');
            Route::post('/', ['\\' . AutomationActionsController::class, 'store'])->name('mailcoach.automations.actions.store');
        });
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\' . AutomationMailsIndexController::class)->name('mailcoach.automations.mails');
    Route::post('/', '\\' . CreateAutomationMailController::class)->name('mailcoach.automations.mails.store');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\' . AutomationMailSummaryController::class)->name('mailcoach.automations.mails.summary');
        Route::post('duplicate', '\\' . DuplicateAutomationMailController::class)->name('mailcoach.automations.mails.duplicate');
        Route::delete('/', '\\' . DestroyAutomationMailController::class)->name('mailcoach.automations.mails.delete');
        Route::get('settings', ['\\' . AutomationMailSettingsController::class, 'edit'])->name('mailcoach.automations.mails.settings');
        Route::put('settings', ['\\' . AutomationMailSettingsController::class, 'update']);
        Route::get('opens', '\\' . AutomationMailOpensController::class)->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\' . AutomationMailClicksController::class)->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\' . AutomationMailUnsubscribesController::class)->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\' . AutomationMailOutboxController::class)->name('mailcoach.automations.mails.outbox');
        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
        Route::put('content', [AutomationMailContentController::class, 'update'])->name('mailcoach.automations.mails.updateContent');
        Route::get('delivery', '\\' . AutomationMailDeliveryController::class)->name('mailcoach.automations.mails.delivery');
        Route::post('send-test-email', '\\' . SendAutomationMailTestController::class)->name('mailcoach.automations.mails.sendTestEmail');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\' . TransactionalMailIndexController::class)->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\' . TransactionalMailContentController::class)->name('mailcoach.transactionalMail.show');
        Route::get('body', '\\' . ShowTransactionalMailBodyController::class)->name('mailcoach.transactionalMail.body');
        Route::get('performance', '\\' . TransactionalMailPerformanceController::class)->name('mailcoach.transactionalMail.performance');
        Route::get('resend', [ResendTransactionalMailController::class, 'show'])->name('mailcoach.transactionalMail.resend');
        Route::post('resend', [ResendTransactionalMailController::class, 'resend']);
        Route::delete('/', '\\' . DestroyTransactionalMailController::class)->name('mailcoach.transactionalMail.delete');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', ['\\' . TransactionalMailTemplatesController::class, 'index'])->name('mailcoach.transactionalMails.templates');

    Route::post('/', ['\\' . TransactionalMailTemplatesController::class, 'store'])->name('mailcoach.transactionalMails.templates.store');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', ['\\' . TransactionalMailTemplatesController::class, 'edit'])->name('mailcoach.transactionalMails.templates.edit');
        Route::put('content', ['\\' . TransactionalMailTemplatesController::class, 'update']);
        Route::delete('/', ['\\' . TransactionalMailTemplatesController::class, 'destroy'])->name('mailcoach.transactionalMails.templates.delete');
        Route::post('duplicate', ['\\' . TransactionalMailTemplatesController::class, 'duplicate'])->name('mailcoach.transactionalMails.templates.duplicate');

        Route::get('settings', ['\\' . TransactionalMailSettingsController::class, 'edit'])->name('mailcoach.transactionalMails.templates.settings');
        Route::put('settings', ['\\' . TransactionalMailSettingsController::class, 'update']);
        Route::post('send-test-email', '\\' . SendTransactionalMailTestController::class)->name('mailcoach.transactionalMails.templates.sendTestEmail');
    });
});

Route::prefix('subscriber-import')->group(function () {
    Route::get('{subscriberImport}/download-attachment/{collection}', '\\' . DownloadSubscriberImportAttachmentController::class)->name('mailcoach.subscriberImport.downloadAttachment');
    Route::delete('{subscriberImport}', '\\' . DestroySubscriberImportController::class)->name('mailcoach.subscriberImport.delete');
});

Route::prefix('subscriber/{subscriber}')->group(function () {
    Route::post('resend-confirmation-mail', '\\' . ResendConfirmationMailController::class)->name('mailcoach.subscriber.resend-confirmation-mail');
    Route::post('confirm', '\\' . ConfirmController::class)->name('mailcoach.subscriber.confirm');
    Route::post('unsubscribe', '\\' . UnsubscribeController::class)->name('mailcoach.subscriber.unsubscribe');
    Route::post('subscribe', '\\' . ResubscribeController::class)->name('mailcoach.subscriber.resubscribe');
});

Route::prefix('templates')->group(function () {
    Route::get('/', ['\\' . TemplatesController::class, 'index'])->name('mailcoach.templates');
    Route::post('/', ['\\' . TemplatesController::class, 'store'])->name('mailcoach.templates.store');
    Route::prefix('{template}')->group(function () {
        Route::get('/', ['\\' . TemplatesController::class, 'edit'])->name('mailcoach.templates.edit');
        Route::put('/', ['\\' . TemplatesController::class, 'update']);
        Route::delete('/', ['\\' . TemplatesController::class, 'destroy'])->name('mailcoach.templates.delete');
        Route::post('duplicate', ['\\' . TemplatesController::class, 'duplicate'])->name('mailcoach.templates.duplicate');
    });
});
