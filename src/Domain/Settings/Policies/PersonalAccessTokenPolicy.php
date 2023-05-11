<?php

namespace Spatie\Mailcoach\Domain\Settings\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Mailcoach\Domain\Settings\Models\PersonalAccessToken;
use Spatie\Mailcoach\Domain\Settings\Models\User;

class PersonalAccessTokenPolicy
{
    use HandlesAuthorization;

    public function administer(User $user, PersonalAccessToken $personalAccessToken)
    {
        return $user->id === $personalAccessToken->user()->id;
    }
}
