<x-mailcoach::layout :title="__mc('Debug')">

@php($issueBody = "## Describe your issue\n\n\n\n---\n## Health check:\n\n")
<div class="card-grid form-fieldsets-no-max-w">
    <x-mailcoach::fieldset card :legend="__mc('Health')">
        <dl class="dl markup-links">
            @php($issueBody.='**Environment**: ' . app()->environment() . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!app()->environment('local')" warning="true" :label="__mc('Environment')" />
            </dt>
            <dd>
                <div>
                    {{ app()->environment() }}
                </div>
            </dd>

            @php($issueBody.='**Debug**: ' . (config('app.debug') ? 'ON' : 'OFF') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!config('app.debug')" warning="true" :label="__mc('Debug')" />
            </dt>
            <dd>
                {{ config('app.debug') ? 'ON' : 'OFF' }}
            </dd>

            @if(! $usesVapor)
                @php($issueBody.='**Horizon**: ' . ($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE) ? 'Active' : 'Inactive') . "\n")
                <dt>
                    <x-mailcoach::health-label reverse :test="$horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE)" :label="__mc('Horizon')" />
                </dt>
                <dd>
                    <p>
                    @if($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE))
                        {{ __mc('Active') }}
                    @else
                        {!! __mc('Horizon is inactive. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                    </p>
                </dd>

                @php($issueBody.='**Queue** connection: ' . ($hasQueueConnection ? 'OK' : 'Not OK') . "\n")
                <dt>
                    <x-mailcoach::health-label reverse :test="$hasQueueConnection"  :label="__mc('Queue connection')" />
                </dt>
                <dd>
                    <p>
                        @if($hasQueueConnection)
                        {!! __mc('Queue connection settings for <code>mailcoach-redis</code> exist.') !!}
                        @else
                            {!! __mc('No valid <strong>queue connection</strong> found. Configure a queue connection with the <strong>mailcoach-redis</strong> key. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                        @endif
                    </p>
                </dd>
            @endif

            @php($issueBody.='**Webhooks**: ' . $webhookTableCount . " unprocessed webhooks\n")
            <dt>
                <x-mailcoach::health-label reverse :test="$webhookTableCount === 0"  :label="__mc('Webhooks')" />
            </dt>
            <dd>
                @if($webhookTableCount === 0)
                    {{ __mc('All webhooks are processed.') }}
                @else
                    {{ __mc(':count unprocessed webhooks.', ['count' => $webhookTableCount ]) }}
                @endif
            </dd>

            <dt>
                @if ($lastScheduleRun && now()->diffInMinutes($lastScheduleRun) < 10)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <x-mailcoach::health-label reverse :test="true"  :label="__mc('Schedule')" />
                @elseif ($lastScheduleRun)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <x-mailcoach::health-label reverse :test="false" warning="true" :label="__mc('Schedule')" />
                @else
                    @php($issueBody.="**Schedule**: hasn't run\n")
                    <x-mailcoach::health-label reverse :test="false" :label="__mc('Schedule')" />
                @endif
            </dt>
            <dd>
                @if ($lastScheduleRun)
                    {{ __mc('Ran :lastRun minute(s) ago.', ['lastRun' => now()->diffInMinutes($lastScheduleRun) ]) }}
                @else
                     {{ __mc('Schedule hasn\'t run.') }}
                @endif
            </dd>
            <dt>

            </dt>
            <dd>
                @if ($scheduledJobs->count())
                    <?php /** @var \Illuminate\Console\Scheduling\Event $scheduledJob */ ?>
                    <table class="table-styled">
                        <thead>
                            <th class="w-36">Schedule</th>
                            <th>Command</th>
                            <th class="w-40">Background</th>
                            <th class="w-40">No overlap</th>
                        </thead>
                        @foreach($scheduledJobs as $scheduledJob)
                            <tr>
                                <td>
                                    <code>
                                        {{ $scheduledJob->expression }}
                                    </code>
                                </td>
                                <td class="">
                                    <code>
                                        {{ \Illuminate\Support\Str::after($scheduledJob->command, '\'artisan\' ') }}
                                    </code>
                                </td>
                                <td>
                                    @if ($scheduledJob->runInBackground)
                                        <x-mailcoach::rounded-icon type="success" icon="fa-fw fas fa-check"/>
                                    @endif
                                </td>
                                <td>
                                    @if ($scheduledJob->withoutOverlapping)
                                        <x-mailcoach::rounded-icon type="success" icon="fa-fw fas fa-check"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    No scheduled jobs!
                @endif
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__mc('Filesystem configuration')">
        <dl class="dl">
            @foreach($filesystems as $key => $filesystem)
                @php($issueBody.="**{$key} disk**: " . $filesystem['disk'] . " (visibility: " . $filesystem['visibility'] . ")\n")
                <dt>
                    <x-mailcoach::health-label
                        :test="$filesystem['disk'] !== 'public' && $filesystem['visibility'] !== 'public'"
                        :label="$key"
                    />
                </dt>
                <dd class="block">
                    <code>
                        {{ $filesystem['disk'] }}
                    </code>
                    (visibility: {{ $filesystem['visibility'] }})
                </dd>
            @endforeach
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__mc('Mailers')">

        <dl class="dl">
            @php($issueBody.="**Default mailer**: " . config('mail.default') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mail.default'), ['log', 'array', null])" warning="true" :label="__mc('Default mailer')" />
            </dt>
            <dd>
                <code>{{ config('mail.default') }}</code>
            </dd>

            @php($issueBody.="**Mailcoach mailer**: " . (config('mailcoach.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.mailer'), ['log', 'array'])" warning="true" :label="__mc('Mailcoach mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.mailer') ?? 'null' }}</code>
            </dd>

            @php($issueBody.="**Campaign mailer**: " . (config('mailcoach.campaigns.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.campaigns.mailer'), ['log', 'array'])" warning="true" :label="__mc('Campaign mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.campaigns.mailer') ?? 'null' }}</code>
            </dd>

            @php($issueBody.="**Transactional mailer**: " . (config('mailcoach.transactional.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.transactional.mailer'), ['log', 'array'])" warning="true" :label="__mc('Transactional mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.transactional.mailer') ?? 'null' }}</code>
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Technical Details')">
        @php($issueBody.="\n\n## Technical details\n\n")
        <dl class="dl">
                @php($issueBody.="**App directory**: " . base_path() . "\n")
                <dt>App directory</dt>
                <dd>
                    <code>{{ base_path() }}</code>
                </dd>

                @php($issueBody.="**User agent**: " . request()->userAgent() . "\n")
                <dt>User agent</dt>
                <dd>
                    <code>{{ request()->userAgent() }}</code>
                </dd>

                @php($issueBody.="**PHP version**: " . PHP_VERSION . "\n")
                <dt>PHP</dt>
                <dd>
                    <code>{{ PHP_VERSION }}</code>
                </dd>

                @php($issueBody.="**" . config('database.default') . " version**: " . $mysqlVersion . "\n")
                <dt>{{ config('database.default') }}</dt>
                <dd>
                    <code>{{ $mysqlVersion }}</code>
                </dd>

                @php($issueBody.="**Laravel version**: " . app()->version() . "\n")
                <dt>Laravel</dt>
                <dd>
                    <code>{{ app()->version() }}</code>
                </dd>

                @php($issueBody.="**Horizon version**: " . $horizonVersion . "\n")
                <dt>Horizon</dt>
                <dd>
                    <code>{{ $horizonVersion }}</code>
                </dd>

                @php($issueBody.="**laravel-mailcoach version**: " . $versionInfo->getCurrentVersion('laravel-mailcoach') . "\n")
                <dt>laravel-mailcoach</dt>
                <dd>
                    <div class="flex items-center space-x-2">
                        <code>{{ $versionInfo->getCurrentVersion('laravel-mailcoach') }}</code>
                        @if(! $versionInfo->isLatest('laravel-mailcoach'))
                            <span class="font-sans text-xs inline-flex items-center bg-gray-200 bg-opacity-50 text-gray-600 rounded-sm px-1 leading-relaxed">
                                <i class="fas fa-horse-head opacity-75 mr-1"></i>
                                {{ __mc('Upgrade available') }}
                            </span>
                        @endif
                    </div>
                </dd>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card  :legend="__mc('Having trouble?')">
        <a href="https://github.com/spatie/laravel-mailcoach/issues/new?body={{ urlencode($issueBody) }}" target="_blank">
            <x-mailcoach::button :label="__mc('Create a support issue')"/>
        </a>
    </x-mailcoach::fieldset>
</div>
</x-mailcoach::layout>
