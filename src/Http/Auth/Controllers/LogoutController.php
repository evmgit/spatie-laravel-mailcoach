<?php

namespace Spatie\Mailcoach\Http\Auth\Controllers;

class LogoutController extends LoginController
{
    public function __construct()
    {
        // empty constructor to avoid the parent applying unwanted middleware
    }

    public function __invoke()
    {
        $this->guard()->logout();

        session()->flush();

        flash()->success(__mc('You have been logged out.'));

        return redirect()->action([LoginController::class, 'showLoginForm']);
    }
}
