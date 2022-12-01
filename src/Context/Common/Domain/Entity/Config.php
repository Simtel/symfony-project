<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Entity;

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
}
