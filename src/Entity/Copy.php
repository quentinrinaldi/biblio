<?php

namespace App\Entity;

use App\Repository\CopyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CopyRepository::class)
 */
class Copy
{
    const STATUS_AVAILABLE = 'AVAILABLE';
    const STATUS_BORROWED = 'BORROWED';
    const STATUS_UNUSABLE = 'UNUSABLE';

    const PHYSICAL_STATE = [0 =>'AS_NEW', 1 => 'VERY_GOOD', 2 =>'GOOD', 3 => 'USED', 4 => 'DAMAGED'];
    const STATUS = [self::STATUS_AVAILABLE, self::STATUS_BORROWED, self::STATUS_UNUSABLE];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="copies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=COPY::PHYSICAL_STATE, message="Choose a valid physical state.")
     */
    private $physicalState = self::PHYSICAL_STATE[0];

    /**
     * @ORM\OneToMany(targetEntity=Borrowing::class, mappedBy="copy")
     */
    private $borrowings;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=COPY::STATUS, message="Choose a valid status.")

     */
    private $status = 'AVAILABLE';

    public function __construct()
    {
        $this->borrowings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getPhysicalState(): ?string
    {
        return $this->physicalState;
    }

    public function setPhysicalState(string $physicalState): self
    {
        $this->physicalState = $physicalState;

        return $this;
    }

    /**
     * @return Collection|Borrowing[]
     */
    public function getBorrowings(): Collection
    {
        return $this->borrowings;
    }

    public function addBorrowing(Borrowing $borrowing): self
    {
        if (!$this->borrowings->contains($borrowing)) {
            $this->borrowings[] = $borrowing;
            $borrowing->setCopy($this);
        }

        return $this;
    }

    public function removeBorrowing(Borrowing $borrowing): self
    {
        if ($this->borrowings->contains($borrowing)) {
            $this->borrowings->removeElement($borrowing);
            // set the owning side to null (unless already changed)
            if ($borrowing->getCopy() === $this) {
                $borrowing->setCopy(null);
            }
        }

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
}
