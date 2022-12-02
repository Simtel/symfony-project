<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Entity;

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

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $updateAt;

    /**
     * @param string $name
     * @param string|null $value
     */
    public function __construct(string $name, ?string $value)
    {
        $this->name = $name;
        $this->value = $value;
        $this->updateAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
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
}
