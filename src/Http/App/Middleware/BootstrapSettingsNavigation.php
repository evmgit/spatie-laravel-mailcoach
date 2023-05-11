<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Navigation\Section;

class BootstrapSettingsNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        resolve(SettingsNavigation::class)
            ->add(__mc('Profile'), route('account'))
            ->add(__mc('Password'), route('password'))
            ->add(__mc('Users'), route('users'))
            ->add(__mc('Configuration'), route('general-settings'), function (Section $section) {
                $section
                    ->add(__mc('General'), route('general-settings'))
                    ->add(__mc('Mailers'), route('mailers'))
                    ->add(__mc('Editor'), route('editor'))
                    ->add(__mc('API Tokens'), route('tokens'))
                    ->add(__mc('Webhooks'), route('webhooks'));
            });

        return $next($request);
    }
}
