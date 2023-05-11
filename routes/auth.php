<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Auth\Controllers\ForgotPasswordController;
use Spatie\Mailcoach\Http\Auth\Controllers\LoginController;
use Spatie\Mailcoach\Http\Auth\Controllers\LogoutController;
use Spatie\Mailcoach\Http\Auth\Controllers\ResetPasswordController;
use Spatie\Mailcoach\Http\Auth\Controllers\WelcomeController;
use Spatie\WelcomeNotification\WelcomesNewUsers;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('mailcoach.login');
Route::post('login', [LoginController::class, 'login']);

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('mailcoach.forgot-password');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('mailcoach.password.email');

Route::get('reset-password', [ResetPasswordController::class, 'showResetForm'])->name('mailcoach.password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('mailcoach.password.update');

Route::group(['middleware' => ['web', WelcomesNewUsers::class]], function () {
    Route::get('welcome/{user}', [WelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [WelcomeController::class, 'savePassword']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', LogoutController::class)->name('mailcoach.logout');
});
