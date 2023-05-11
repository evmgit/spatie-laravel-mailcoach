<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes)
    {
        return $this->getTransactionalMailClass()::create([
            'name' => $attributes['name'],
            'type' => $attributes['type'],
        ]);
    }
}
