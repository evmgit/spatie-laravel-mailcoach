<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\TemplateFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Template extends Model implements HasHtmlContent
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
    ];

    public function getTemplateFieldValues(): array
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        return $structuredHtml['templateValues'] ?? ['html' => $this->getHtml()];
    }

    public function setTemplateFieldValues(array $fieldValues = []): self
    {
        $structuredHtml = json_decode($this->getStructuredHtml(), true) ?? [];

        $structuredHtml['templateValues'] = $fieldValues;

        $this->structured_html = json_encode($structuredHtml);

        return $this;
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany($this->getCampaignClass());
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    protected static function newFactory(): TemplateFactory
    {
        return new TemplateFactory();
    }

    public function containsPlaceHolders(): bool
    {
        return (new TemplateRenderer($this->getHtml()))->containsPlaceHolders();
    }

    public function placeHolderNames(): array
    {
        return (new TemplateRenderer($this->getHtml()))->placeHolderNames();
    }

    public function fields(): array
    {
        return (new TemplateRenderer($this->getHtml()))->fields();
    }

    public function hasTemplates(): bool
    {
        return false;
    }
}
