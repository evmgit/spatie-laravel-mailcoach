<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag as TagModel;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class TagComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;

    public EmailList $emailList;

    public TagModel $tag;

    protected function rules()
    {
        return [
            'tag.name' => [
                'required',
                Rule::unique('mailcoach_tags', 'name')
                    ->where('email_list_id', $this->emailList->id)
                    ->ignore($this->tag->id),
            ],
            'tag.visible_in_preferences' => ['required', 'bool'],
        ];
    }

    public function mount(EmailList $emailList, TagModel $tag)
    {
        $this->authorize('update', $emailList);
        $this->authorize('update', $tag);

        $this->emailList = $emailList;
        $this->tag = $tag;

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Tags'), route('mailcoach.emailLists.tags', $this->emailList));
            });
    }

    public function save()
    {
        $this->validate();

        $this->tag->save();

        $this->flash(__mc('Tag :tag was updated', ['tag' => $this->tag->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.tags.show')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'emailList' => $this->emailList,
                'title' => $this->tag->name,
            ]);
    }
}
