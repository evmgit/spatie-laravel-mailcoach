<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support;

use Symfony\Component\Mime\Address;

class AddressNormalizer
{
    /**
     * @param  string|null  $adresses
     * @return array<int, Address>
     */
    public function normalize(?string $adresses): array
    {
        if (is_null($adresses)) {
            return [];
        }

        return str($adresses)
            ->squish()
            ->explode(',')
            ->map(fn (string $address) => Address::create($address))
            ->toArray();
    }
}
