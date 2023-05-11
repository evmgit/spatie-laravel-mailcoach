<?php

namespace Spatie\Mailcoach\Domain\Audience\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;

class EmailListPolicy
{
    public function __call($method, $args): bool
    {
        /** @var Authorizable $user */
        $user = array_shift($args);

        return $user->can('viewMailcoach');
    }
}
