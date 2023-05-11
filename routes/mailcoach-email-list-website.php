<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListWebsiteController;

Route::get('{emailListWebsiteSlug?}', [EmailListWebsiteController::class, 'index'])->name('mailcoach.website');
Route::get('{emailListWebsiteSlug?}/{campaignUuid}', [EmailListWebsiteController::class, 'show'])
    ->name('mailcoach.website.campaign');
