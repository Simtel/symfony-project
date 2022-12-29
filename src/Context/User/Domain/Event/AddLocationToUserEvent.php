<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Event;

use App\Context\User\Domain\Entity\Location;
use App\Context\User\Domain\Entity\User;
use App\Context\User\Infrastructure\EventListener\AddLocationToUserEventListener;

/**
 * @see AddLocationToUserEventListener
 */
readonly class AddLocationToUserEvent
{
    public function __construct(
        private User $user,
        private Location $location,
    ) {
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }
}
