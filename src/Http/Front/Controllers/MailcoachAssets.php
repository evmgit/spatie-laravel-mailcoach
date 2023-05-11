<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Livewire\Controllers\CanPretendToBeAFile;

class MailcoachAssets
{
    use CanPretendToBeAFile;

    public function script()
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../../../../resources/dist/manifest.json'), true);

        $fileName = $manifest['resources/js/app.js']['file'];

        return $this->pretendResponseIsFile(__DIR__."/../../../../resources/dist/{$fileName}");
    }

    public function style()
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../../../../resources/dist/manifest.json'), true);

        $fileName = $manifest['resources/css/app.css']['file'];

        return $this->pretendResponseIsFile(__DIR__."/../../../../resources/dist/{$fileName}", 'text/css');
    }
}
