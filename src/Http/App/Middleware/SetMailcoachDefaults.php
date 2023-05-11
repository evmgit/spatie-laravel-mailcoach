<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Auth\Notifications\ResetPassword;
use Spatie\Flash\Flash;

class SetMailcoachDefaults
{
    public function handle($request, $next)
    {
        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);

        if (config('mailcoach.guard')) {
            config()->set('auth.defaults.guard', config('mailcoach.guard'));
        }

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('mailcoach.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        return $next($request);
    }
}
