<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */

class User implements UserInterface
{

    const STATUS = ['AWAITING_FINE_PAYMENT', 'EXPIRED_SUBSCRIPTION', 'IN_ORDER' ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date")
     */
    private $subscriptionExpirationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=USER::STATUS, message="Choose a valid status.")
     */
    private $status = 'IN_ORDER';

    /**
     * @ORM\OneToMany(targetEntity=Borrowing::class, mappedBy="user")
     */
    private $currentBorrowings;

    /**
     * @ORM\OneToMany(targetEntity=Penalty::class, mappedBy="user")
     */
    private $penalties;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->currentBorrowings = new ArrayCollection();
        $this->penalties = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $today = new \DateTime('now');
        $this->subscriptionExpirationDate = $today->add(new \DateInterval("P1Y"));

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSubscriptionExpirationDate(): ?\DateTimeInterface
    {
        return $this->subscriptionExpirationDate;
    }

    public function setSubscriptionExpirationDate(\DateTimeInterface $subscriptionExpirationDate): self
    {
        $this->subscriptionExpirationDate = $subscriptionExpirationDate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Borrowing[]
     */
    public function getCurrentBorrowings(): Collection
    {
        return $this->currentBorrowings;
    }

    public function addCurrentBorrowing(Borrowing $currentBorrowing): self
    {
        if (!$this->currentBorrowings->contains($currentBorrowing)) {
            $this->currentBorrowings[] = $currentBorrowing;
            $currentBorrowing->setUser($this);
        }

        return $this;
    }

    public function removeCurrentBorrowing(Borrowing $currentBorrowing): self
    {
        if ($this->currentBorrowings->contains($currentBorrowing)) {
            $this->currentBorrowings->removeElement($currentBorrowing);
            // set the owning side to null (unless already changed)
            if ($currentBorrowing->getUser() === $this) {
                $currentBorrowing->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Penalty[]
     */
    public function getPenalties(): Collection
    {
        return $this->penalties;
    }

    public function addPenalty(Penalty $penalty): self
    {
        if (!$this->penalties->contains($penalty)) {
            $this->penalties[] = $penalty;
            $penalty->setUser($this);
        }

        return $this;
    }

    public function removePenalty(Penalty $penalty): self
    {
        if ($this->penalties->contains($penalty)) {
            $this->penalties->removeElement($penalty);
            // set the owning side to null (unless already changed)
            if ($penalty->getUser() === $this) {
                $penalty->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
