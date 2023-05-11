<?php

namespace Spatie\Mailcoach\Domain\Automation\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;

class AutomationPolicy
{
    public function __call($method, $args): bool
    {
        /** @var Authorizable $user */
        $user = array_shift($args);

        return $user->can('viewMailcoach');
    }
}
