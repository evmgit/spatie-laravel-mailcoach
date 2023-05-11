<div class="grid items-start justify-start gap-x-16 gap-y-8 md:grid-cols-[auto,auto]">
    @foreach ($actionOptions as $category => $actions)
        <div>
            <h4 class="mb-2 markup-h4">
                <x-mailcoach::rounded-icon size="md" minimal type="info" icon="fa-fw fas {{ \Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum::icons()[$category] }}" />

                {{ \Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum::from($category)->label() }}
            </h4>
            <ul>
                @foreach ($actions as $action)
                    <li>
                        <a class="block link py-2 whitespace-nowrap" href="#" wire:click.prevent="addAction('{{ addslashes($action) }}', {{ $index }})">
                            {{ $action::getName() }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
