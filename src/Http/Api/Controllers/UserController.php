<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Spatie\Mailcoach\Http\Api\Resources\UserResource;

class UserController
{
    public function __invoke()
    {
        return new UserResource(auth()->user());
    }
}
