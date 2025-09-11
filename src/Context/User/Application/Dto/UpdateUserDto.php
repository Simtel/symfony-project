<?php

declare(strict_types=1);

namespace App\Context\User\Application\Dto;

use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;

final class UpdateUserDto
{
    /** @var Location[] */
    private array $locations = [];

    /**
     * @var UserContactDto[]
     */
    private array $contacts = [];

    public function __construct(
        private readonly User $user
    ) {
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addLocation(Location $location): void
    {
        $this->locations[] = $location;
    }

    /**
     * @return UserContactDto[]
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }
}
