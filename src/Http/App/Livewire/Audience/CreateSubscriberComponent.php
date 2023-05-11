<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Rules\EmailListSubscriptionRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateSubscriberComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $email = null;

    public ?string $first_name = null;

    public ?string $last_name = null;

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    protected function rules()
    {
        return [
            'email' => ['required', 'email:rfc', new EmailListSubscriptionRule($this->emailList)],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
        ];
    }

    public function saveSubscriber()
    {
        $subscriberClass = self::getSubscriberClass();

        $this->authorize('create', $subscriberClass);

        $validated = $this->validate();

        $subscriberClass::createWithEmail($validated['email'])
            ->withAttributes([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ])
            ->skipConfirmation()
            ->subscribeTo($this->emailList);

        flash()->success(__mc('Subscriber :subscriber was created.', ['subscriber' => $this->email]));

        return redirect()->route('mailcoach.emailLists.subscribers', $this->emailList);
    }

    public function render()
    {
        return view('mailcoach::app.emailLists.subscribers.partials.create');
    }
}
