<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BorrowingRepository::class)
 */
class Borrowing
{
    const STATUS_READING_IN_PROGRESS = 'READING_IN_PROGRESS';
    const STATUS_PICKUP_PENDING = 'PICKUP_PENDING';
    const STATUS_RETURNED_OK = 'RETURNED_OK';
    const STATUS_RETURNED_LATE = 'RETURNED_LATE';
    const STATUS_AWAITING_RETURN = 'AWAITING_RETURN';
    const STATUS_RETURNED_ARRAY = [self::STATUS_RETURNED_OK, self::STATUS_RETURNED_LATE];
    const STATUS_IN_PROGRESS_ARRAY = [self::STATUS_READING_IN_PROGRESS, self::STATUS_PICKUP_PENDING, self::STATUS_AWAITING_RETURN ];

    const STATUS = ['PICKUP_PENDING', 'READING_IN_PROGRESS', 'RETURNED_OK', 'RETURNED_LATE', 'AWAITING_RETURN' ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date")
     */
    private $expectedReturnDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $returnedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Borrowing::STATUS, message="Choose a valid status.")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="currentBorrowings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Copy::class, inversedBy="borrowings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $copy;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExpectedReturnDate(): ?\DateTimeInterface
    {
        return $this->expectedReturnDate;
    }

    public function setExpectedReturnDate(\DateTimeInterface $expectedReturnDate): self
    {
        $this->expectedReturnDate = $expectedReturnDate;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeInterface
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeInterface $returnedAt): self
    {
        $this->returnedAt = $returnedAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCopy(): ?Copy
    {
        return $this->copy;
    }

    public function setCopy(?Copy $copy): self
    {
        $this->copy = $copy;

        return $this;
    }
}
