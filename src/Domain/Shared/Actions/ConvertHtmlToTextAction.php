<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Exception;
use League\HTMLToMarkdown\HtmlConverter;

class ConvertHtmlToTextAction
{
    public function execute(string $html): string
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'suppress_errors' => false,
            'remove_nodes' => 'head script style',
        ]);

        try {
            $text = $converter->convert($html);
        } catch (Exception) {
            $text = '';
        }

        return $text;
    }
}
