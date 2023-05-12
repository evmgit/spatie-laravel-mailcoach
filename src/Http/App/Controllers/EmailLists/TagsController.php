<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Http\App\Queries\EmailListTagsQuery;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\CreateTagRequest;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\UpdateTagRequest;

class TagsController
{
    use AuthorizesRequests;

    public function index(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $tagsQuery = new EmailListTagsQuery($emailList);

        return view('mailcoach::app.emailLists.tags.index', [
            'emailList' => $emailList,
            'tags' => $tagsQuery->paginate(),
            'totalTagsCount' => Tag::query()->emailList($emailList)->count(),
            'totalDefault' => Tag::query()->where('type', TagType::DEFAULT)->emailList($emailList)->count(),
            'totalMailcoach' => Tag::query()->where('type', TagType::MAILCOACH)->emailList($emailList)->count(),
        ]);
    }

    public function store(CreateTagRequest $request, EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        $tag = $emailList->tags()->create([
            'name' => $request->name,
            'type' => TagType::DEFAULT,
        ]);

        flash()->success(__('Tag :tag was created', ['tag' => $tag->name]));

        return back();
    }

    public function edit(EmailList $emailList, Tag $tag)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.tags.edit', [
            'emailList' => $emailList,
            'tag' => $tag,
        ]);
    }

    public function update(UpdateTagRequest $request, EmailList $emailList, Tag $tag)
    {
        $this->authorize('update', $emailList);

        $tag->update([
            'name' => $request->name,
        ]);

        flash()->success(__('Tag :tag was updated', ['tag' => $tag->name]));

        return redirect()->route('mailcoach.emailLists.tags', $emailList);
    }

    public function destroy(EmailList $emailList, Tag $tag)
    {
        $this->authorize('update', $emailList);

        $tag->subscribers->each(function ($subscriber) use ($tag) {
            event(new TagRemovedEvent($subscriber, $tag));
        });

        $tag->delete();

        flash()->success(__('Tag :tag was deleted', ['tag' => $tag->name]));

        return back();
    }
}
