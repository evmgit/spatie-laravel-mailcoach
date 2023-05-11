<?php

namespace Spatie\Mailcoach\Domain\Shared\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;

class SendPolicy
{
    public function __call($method, $args): bool
    {
        /** @var Authorizable $user */
        $user = array_shift($args);

        return $user->can('viewMailcoach');
    }
}
