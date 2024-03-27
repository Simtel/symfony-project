<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Entity;

use App\Context\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private ?string $value;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updateAt;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private User $createdBy;


    public function __construct(string $name, ?string $value, User $user)
    {
        $this->name = $name;
        $this->value = $value;
        $this->createdBy = $user;

        $this->updateAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getUpdateAt(): DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
