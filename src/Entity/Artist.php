<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 */
class Artist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=ArtistInvolvement::class, mappedBy="artist")
     */
    private $artistInvolvements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $presentation;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deathDate;

    public function __construct()
    {
        $this->artistInvolvements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|ArtistInvolvement[]
     */
    public function getArtistInvolvements(): Collection
    {
        return $this->artistInvolvements;
    }

    public function addArtistInvolvement(ArtistInvolvement $artistInvolvement): self
    {
        if (!$this->artistInvolvements->contains($artistInvolvement)) {
            $this->artistInvolvements[] = $artistInvolvement;
            $artistInvolvement->setArtist($this);
        }

        return $this;
    }

    public function removeArtistInvolvement(ArtistInvolvement $artistInvolvement): self
    {
        if ($this->artistInvolvements->contains($artistInvolvement)) {
            $this->artistInvolvements->removeElement($artistInvolvement);
            // set the owning side to null (unless already changed)
            if ($artistInvolvement->getArtist() === $this) {
                $artistInvolvement->setArtist(null);
            }
        }

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

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getDeathDate(): ?\DateTimeInterface
    {
        return $this->deathDate;
    }

    public function setDeathDate(?\DateTimeInterface $deathDate): self
    {
        $this->deathDate = $deathDate;

        return $this;
    }
}
