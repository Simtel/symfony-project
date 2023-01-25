<?php

namespace App\Context\User\Domain\Entity;

use App\Context\Common\Application\Contract\EntityEventInterface;
use App\Context\User\Domain\Event\AddLocationToUserEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity()]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $token;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $secretKey = null;

    /** @var EntityEventInterface[] */
    private array $events = [];

    /**
     * @var Collection<int, Location>
     */
    #[ORM\JoinTable(name: 'user_location')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'location_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Location::class, fetch: 'EXTRA_LAZY')]
    private Collection $locations;

    public function __construct(string $email, string $name, string $password)
    {
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;

        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        if ($this->name === $name) {
            return;
        }
        $this->name = $name;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        /* do nothing */
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getId();
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations->toArray();
    }

    public function addLocation(Location $location): void
    {
        $this->locations->add($location);
        $this->events[] = new AddLocationToUserEvent($this, $location);
    }

    /**
     * @return EntityEventInterface[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    #[PrePersist]
    public function fillSecretKeyBeforePersist(PrePersistEventArgs $eventArgs): void
    {
        $this->secretKey = sha1($this->getName() . '/' . $this->getPassword());
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }
}
