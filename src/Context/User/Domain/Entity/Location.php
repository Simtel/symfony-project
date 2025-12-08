<?php

namespace App\Context\User\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @param string $name
     */
    public function __construct(
        #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
        private string $name
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
