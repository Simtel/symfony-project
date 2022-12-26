<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\View;

use App\Context\User\Domain\Entity\Location;

readonly class LocationView
{
    public function __construct(private Location $location)
    {
    }

    public function getName(): string
    {
        return $this->location->getName();
    }
}
