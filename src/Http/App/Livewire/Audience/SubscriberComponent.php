<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber as SubscriberModel;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;

class SubscriberComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public SubscriberModel $subscriber;

    public EmailList $emailList;

    public int $totalSendsCount;

    public array $tags = [];

    public string $tab = 'profile';

    protected $queryString = [
        'tab' => ['except' => 'profile'],
    ];

    protected $listeners = [
        'tags-updated' => 'updateTags',
    ];

    protected function rules(): array
    {
        return [
            'subscriber.email' => [
                'email:rfc',
                Rule::unique(self::getSubscriberTableName(), 'email')
                    ->where('email_list_id', $this->emailList->id)
                    ->ignore($this->subscriber->id),
            ],
            'subscriber.first_name' => 'nullable|string',
            'subscriber.last_name' => 'nullable|string',
            'tags' => 'array',
            'subscriber.extra_attributes' => ['nullable', 'array'],
        ];
    }

    public function updateTags(array|string $tags)
    {
        $this->tags = Arr::wrap($tags);
    }

    public function save()
    {
        $data = $this->validate();

        $updateSubscriberAction = Mailcoach::getAudienceActionClass('update_subscriber', UpdateSubscriberAction::class);

        $updateSubscriberAction->execute(
            $this->subscriber,
            [
                'email' => $data['subscriber']['email'],
                'first_name' => $data['subscriber']['first_name'],
                'last_name' => $data['subscriber']['last_name'],
                'extra_attributes' => $data['subscriber']['extra_attributes'],
            ],
            $data['tags'] ?? [],
        );

        $this->flash(__mc('Subscriber :subscriber was updated.', ['subscriber' => $this->subscriber->email]));
    }

    public function mount(EmailList $emailList, SubscriberModel $subscriber)
    {
        $this->authorize('update', $subscriber);

        $this->emailList = $emailList;
        $this->subscriber = $subscriber;
        $this->totalSendsCount = $subscriber->sends()->count();
        $this->tags = $subscriber->tags()->where('type', TagType::Default)->pluck('name')->toArray();

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Subscribers'), route('mailcoach.emailLists.subscribers', $this->emailList));
            });
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.subscribers.show', [

        ])->layout('mailcoach::app.emailLists.layouts.emailList', [
            'emailList' => $this->emailList,
            'title' => $this->subscriber->email,
        ]);
    }
}
