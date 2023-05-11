<?php

namespace Spatie\Mailcoach\Http\App\ViewComposers;

use Illuminate\View\View;
use Spatie\Mailcoach\Domain\Shared\Support\Version;

class FooterComposer
{
    public function compose(View $view)
    {
        $view->with('versionInfo', resolve(Version::class));
    }
}
