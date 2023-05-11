<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;

class TransactionalMailPolicy
{
    public function __call($method, $args): bool
    {
        /** @var Authorizable $user */
        $user = array_shift($args);

        return $user->can('viewMailcoach');
    }
}
