<?php

namespace Spatie\Mailcoach\Domain\Campaign\Policies;

use Illuminate\Contracts\Auth\Access\Authorizable;

class TemplatePolicy
{
    public function __call($method, $args): bool
    {
        /** @var Authorizable $user */
        $user = array_shift($args);

        return $user->can('viewMailcoach');
    }
}
