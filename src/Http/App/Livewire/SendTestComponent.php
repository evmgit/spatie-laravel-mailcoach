<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class SendTestComponent extends Component
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public Model $model;

    public string $emails = '';

    public string $from_email = '';

    public string $html = '';

    public function mount(Model $model)
    {
        $this->model = $model;
        $this->emails = Auth::user()->email;
        $this->from_email = Auth::user()->email;
    }

    public function sendTest()
    {
        $automationMailClass = self::getAutomationMailClass();

        $this->validate([
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
            'from_email' => ['nullable', 'email', Rule::requiredIf($this->model instanceof $automationMailClass)],
        ], [
            'email.required' => __mc('You must specify at least one e-mail address.'),
            'email.email' => __mc('Not all the given e-mails are valid.'),
        ]);

        $emails = array_map('trim', explode(',', $this->emails));

        if ($this->from_email) {
            config()->set('mail.from.address', $this->from_email);
        }

        if ($this->model instanceof Sendable) {
            try {
                $this->model->sendTestMail($emails);
            } catch (\Throwable $e) {
                $this->flashError($e->getMessage());
                $this->dispatchBrowserEvent('modal-closed', ['modal' => 'send-test']);

                return;
            }

            if (count($emails) > 1) {
                $this->flash(__mc('A test email was sent to :count addresses.', ['count' => count($emails)]));
            } else {
                $this->flash(__mc('A test email was sent to :email.', ['email' => $emails[0]]));
            }
        } else {
            $this->flashError(__mc('Model :model does not support sending tests.', ['model' => $this->model::class]));
        }

        $this->dispatchBrowserEvent('modal-closed', ['modal' => 'send-test']);
    }

    public function render()
    {
        return view('mailcoach::app.components.sendTest');
    }
}
