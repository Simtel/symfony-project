<?php

declare(strict_types=1);

namespace App\Context\User\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Block
{
    #[Column(type: "date_immutable", nullable:  true)]
    private ?DateTimeImmutable $startDate;

    #[Column(type: "date_immutable", nullable: true)]
    private ?DateTimeImmutable $endDate;

    public function __construct(?DateTimeImmutable $startDate = null, ?DateTimeImmutable $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }


    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }
}
