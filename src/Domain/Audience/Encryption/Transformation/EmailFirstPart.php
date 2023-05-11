<?php

namespace Spatie\Mailcoach\Domain\Audience\Encryption\Transformation;

use ParagonIE\CipherSweet\Contract\TransformationInterface;

class EmailFirstPart implements TransformationInterface
{
    public function __invoke(mixed $input): string
    {
        return strtolower(explode('@', $input)[0] ?? '');
    }
}
