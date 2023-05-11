<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\EmailListTagsQuery;
use Spatie\Mailcoach\MainNavigation;

class TagsComponent extends DataTableComponent
{
    protected array $allowedFilters = [
        'type' => ['except' => ''],
    ];

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Tags'), route('mailcoach.emailLists.tags', $this->emailList));
            });
    }

    public function deleteTag(int $id)
    {
        $tag = self::getTagClass()::find($id);

        $this->authorize('delete', $tag);

        $tag->subscribers->each(function ($subscriber) use ($tag) {
            event(new TagRemovedEvent($subscriber, $tag));
        });

        $tag->delete();

        $this->flash(__mc('Tag :tag was deleted', ['tag' => $tag->name]));
    }

    public function getTitle(): string
    {
        return __mc('Tags');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.tags.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $tagsQuery = new EmailListTagsQuery($this->emailList, $request);

        return [
            'emailList' => $this->emailList,
            'tags' => $tagsQuery->paginate($request->per_page),
            'totalTagsCount' => self::getTagClass()::query()->emailList($this->emailList)->count(),
            'totalDefault' => self::getTagClass()::query()->where('type', TagType::Default)->emailList($this->emailList)->count(),
            'totalMailcoach' => self::getTagClass()::query()->where('type', TagType::Mailcoach)->emailList($this->emailList)->count(),
        ];
    }
}
