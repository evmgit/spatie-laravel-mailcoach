<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendCampaignController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\EmailListsController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ConfirmSubscriberController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\UnsubscribeController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\AppendSubscriberImportController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\SubscriberImportsController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ShowTransactionalMailController;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\TransactionalMailsController;
use Spatie\Mailcoach\Http\Api\Controllers\UserController;

Route::get('user', UserController::class);

Route::apiResource('templates', TemplatesController::class);

Route::apiResource('campaigns', CampaignsController::class);
Route::prefix('campaigns/{campaign}')->group(function () {
    Route::post('send-test', SendTestEmailController::class);
    Route::post('send', SendCampaignController::class);

    Route::get('opens', CampaignOpensController::class);
    Route::get('clicks', CampaignClicksController::class);
    Route::get('unsubscribes', CampaignUnsubscribesController::class);
});

Route::apiResource('email-lists', EmailListsController::class);
Route::apiResource('email-lists.subscribers', SubscribersController::class)->only(['index', 'store']);

Route::apiResource('subscribers', SubscribersController::class)->except(['index', 'store']);
Route::prefix('subscribers/{subscriber}')->group(function () {
    Route::post('confirm', ConfirmSubscriberController::class);
    Route::post('unsubscribe', UnsubscribeController::class);
    Route::post('resend-confirmation', ResendConfirmationMailController::class);
});

Route::apiResource('subscriber-imports', SubscriberImportsController::class);
Route::prefix('subscriber-imports/{subscriberImport}')->group(function () {
    Route::post('append', AppendSubscriberImportController::class);
    Route::post('start', StartSubscriberImportController::class);
});

Route::prefix('transactional-mails')->group(function () {
    Route::get('/', TransactionalMailsController::class);
    Route::get('{transactionalMail}', ShowTransactionalMailController::class);
    Route::post('{transactionalMail}/resend', ResendTransactionalMailController::class);
});

Route::prefix('automations')->group(function () {
    Route::post('{automation}/trigger', TriggerAutomationController::class);
});
