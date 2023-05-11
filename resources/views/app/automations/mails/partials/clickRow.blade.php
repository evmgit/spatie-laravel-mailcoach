<tr>
    <td class="markup-links"><a class="break-words" href="{{ $row->url }}">{{ $row->url }}</a></td>
    <td><span class="tag-neutral">{{ \Spatie\Mailcoach\Domain\Shared\Support\LinkHasher::hash($row->automationMail, $row->url) }}</span></td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->unique_click_count }}</td>
    <td class="td-numeric">{{ $row->click_count }}</td>
</tr>
