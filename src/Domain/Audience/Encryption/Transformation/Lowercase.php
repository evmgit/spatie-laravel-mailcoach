<?php

namespace Spatie\Mailcoach\Domain\Audience\Encryption\Transformation;

use ParagonIE\CipherSweet\Contract\TransformationInterface;

class Lowercase implements TransformationInterface
{
    public function __invoke(mixed $input): string
    {
        return strtolower($input ?? '');
    }
}
