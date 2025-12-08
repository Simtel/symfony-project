<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Entity;

use App\Context\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity()]
class Config
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updateAt;


    public function __construct(#[ORM\Column(length: 255)]
        private string $name, #[ORM\Column(type: 'text')]
        private ?string $value, #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
        private User $createdBy)
    {
        $this->updateAt = new DateTimeImmutable();
    }

    public function getId(): Uuid
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
