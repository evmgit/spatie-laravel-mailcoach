<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use DOMDocument;

class CreateDomDocumentFromHtmlAction
{
    public function execute(string $html, bool $suppressErrors = true): DOMDocument
    {
        $html = preg_replace('/&(?![^& ]+;)/', '&amp;', $html);

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors($suppressErrors);
        $document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_use_internal_errors($internalErrors);
        $document->formatOutput = true;

        return $document;
    }
}
