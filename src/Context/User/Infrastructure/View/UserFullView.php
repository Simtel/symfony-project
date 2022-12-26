<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\View;

use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;

readonly class UserFullView
{
    public function __construct(private User $user)
    {
    }

    public function getName(): string
    {
        return $this->user->getName();
    }

    public function getId(): ?int
    {
        return $this->user->getId();
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    /**
     * @return LocationView[]
     */
    public function getLocations(): array
    {
        return array_map(
            static fn (Location $location): LocationView => new LocationView($location),
            $this->user->getLocations()
        );
    }
}
