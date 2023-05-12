<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Front\Controllers\AutomationWebviewController;
use Spatie\Mailcoach\Http\Front\Controllers\CampaignWebviewController;
use Spatie\Mailcoach\Http\Front\Controllers\ConfirmSubscriberController;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListCampaignsFeedController;
use Spatie\Mailcoach\Http\Front\Controllers\ReConfirmSubscriberController;
use Spatie\Mailcoach\Http\Front\Controllers\SubscribeController;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeTagController;

Route::get('/confirm-subscription/{subscriberUuid}', '\\' . ConfirmSubscriberController::class)->name('mailcoach.confirm');

Route::get('/reconfirm-subscription/{subscriberUuid}', '\\' . ReConfirmSubscriberController::class)->name('mailcoach.reconfirm');

Route::get('/unsubscribe/{subscriberUuid}/{sendUuid?}',  ['\\' .UnsubscribeController::class, 'show'])->name('mailcoach.unsubscribe');

Route::get('/unsubscribe-tag/{subscriberUuid}/{tag}',  ['\\' . UnsubscribeTagController::class, 'show'])->name('mailcoach.unsubscribe-tag');

Route::post('/unsubscribe/{subscriberUuid}/{sendUuid?}',  ['\\' .UnsubscribeController::class, 'confirm']);

Route::post('/unsubscribe-tag/{subscriberUuid}/{tag}',  ['\\' . UnsubscribeTagController::class, 'confirm']);

Route::get('webview/campaign/{campaignUuid}', '\\' . CampaignWebviewController::class)->name('mailcoach.campaign.webview');
Route::get('webview/automation/{campaignUuid}', '\\' . AutomationWebviewController::class)->name('mailcoach.automations.webview');


Route::get('feed/{emailListUuid}', '\\' . EmailListCampaignsFeedController::class)->name('mailcoach.feed');

Route::post('subscribe/{emailListUuid}', '\\' . SubscribeController::class)->name('mailcoach.subscribe');

Route::prefix('landing')->group(function () {
    Route::view('/subscribed', 'mailcoach::landingPages.subscribed')->name('mailcoach.landingPages.example');
});
