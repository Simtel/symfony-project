<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'contacts')]
        #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE', options: [])]
        private User $user,
        #[ORM\Column(type: 'string', length: 180)]
        private string $code,
        #[ORM\Column(type: 'string', length: 180)]
        private string $name,
        #[ORM\Column(type: 'string', length: 180, nullable: true)]
        private ?string $value
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
