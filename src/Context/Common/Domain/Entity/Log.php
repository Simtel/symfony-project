<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Entity;

use App\Context\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @param string|null $action
     * @param User $author
     */
    public function __construct(#[ORM\Column(type: 'text')]
        private ?string $action, #[ORM\ManyToOne(targetEntity: User::class, )]
        #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
        private User $author)
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getAction(): ?string
    {
        return $this->action;
    }


    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }
}
