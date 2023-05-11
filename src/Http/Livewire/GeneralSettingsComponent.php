<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Support\AppConfiguration\AppConfiguration;
use Spatie\Mailcoach\Domain\Settings\Support\TimeZone;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class GeneralSettingsComponent extends Component
{
    use LivewireFlash;

    public string $name = '';

    public string $timezone = '';

    public string $url = '';

    public string $from_address = '';

    public function rules()
    {
        return [
            'name' => ['required'],
            'timezone' => ['required', Rule::in(TimeZone::all())],
            'url' => ['required', 'url'],
            'from_address' => ['required', 'email:rfc,dns'],
        ];
    }

    public function mount(AppConfiguration $appConfiguration)
    {
        $this->name = $appConfiguration->name ?? config('app.name');
        $this->timezone = $appConfiguration->timezone ?? config('mailcoach.timezone') ?? config('app.timezone');
        $this->url = $appConfiguration->url ?? config('app.url');
        $this->from_address = $appConfiguration->from_address ?? config('mail.from.address') ?? '';
    }

    public function save()
    {
        resolve(AppConfiguration::class)->put($this->validate());

        if (InstalledVersions::isInstalled('laravel/horizon')) {
            dispatch(function () {
                if (app()->runningInConsole()) {
                    Artisan::call('horizon:terminate');
                }
            });
        }

        $this->flash(__mc('The app configuration was saved.'));
    }

    public function render()
    {
        $timeZones = TimeZone::all();

        return view('mailcoach::app.configuration.app.edit', compact('timeZones'))
            ->layout('mailcoach::app.layouts.settings', ['title' => __mc('General')]);
    }
}
