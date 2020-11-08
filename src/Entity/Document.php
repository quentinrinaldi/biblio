<?php

namespace App\Entity;

use App\Entity\Artist;
use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository", repositoryClass=DocumentRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="document_type", type="string")
 * @ORM\DiscriminatorMap({"document" = "Document", "book" = "Book", "dvd" = "Dvd"})
 */
class Document
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $publishedDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url
     */
    protected $thumbnailUrl = "https://via.placeholder.com/200x250?text=+";

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $stars;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $publisher = 'Unknown';

    /**
     * @ORM\OneToMany(targetEntity=Copy::class, mappedBy="document")
     */
    protected $copies;

    /**
     * @ORM\OneToMany(targetEntity=ArtistInvolvement::class, mappedBy="document")
     */
    protected $artistInvolvements;

    /**
     * @ORM\Column(type="date")
     */
    protected $availableSince;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPinned = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $availability = false;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="documents")
     */
    private $categories;

    public function __construct()
    {
        $this->copies = new ArrayCollection();
        $this->artistInvolvements = new ArrayCollection();
        $this->availableSince = new \DateTime('now');
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTimeInterface $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(string $thumbnailUrl): self
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(?int $stars): self
    {
        $this->stars = $stars;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return Collection|Copy[]
     */
    public function getCopies(): Collection
    {
        return $this->copies;
    }

    public function addCopy(Copy $copy): self
    {
        if (!$this->copies->contains($copy)) {
            $this->copies[] = $copy;
            $copy->setDocument($this);
        }

        return $this;
    }

    public function removeCopy(Copy $copy): self
    {
        if ($this->copies->contains($copy)) {
            $this->copies->removeElement($copy);
            // set the owning side to null (unless already changed)
            if ($copy->getDocument() === $this) {
                $copy->setDocument(null);
            }
        }

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
            $artistInvolvement->setDocument($this);
        }

        return $this;
    }

    public function removeArtistInvolvement(ArtistInvolvement $artistInvolvement): self
    {
        if ($this->artistInvolvements->contains($artistInvolvement)) {
            $this->artistInvolvements->removeElement($artistInvolvement);
            // set the owning side to null (unless already changed)
            if ($artistInvolvement->getDocument() === $this) {
                $artistInvolvement->setDocument(null);
            }
        }

        return $this;
    }

    public function getAvailableSince(): ?\DateTimeInterface
    {
        return $this->availableSince;
    }

    public function setAvailableSince(\DateTimeInterface $availableSince): self
    {
        $this->availableSince = $availableSince;

        return $this;
    }



    public function getIsPinned(): ?bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getAvailableCopy() :Copy
    {
        foreach ($this->getCopies() as $copy) {
            if ($copy->getStatus() === 'AVAILABLE')
                return $copy;
        }
        throw new Exception('Available copy not found');
    }

    public function checkAvailability()
    {
        foreach ($this->getCopies() as $copy) {
            if ($copy->getStatus() === 'AVAILABLE')
                return;
        }
        $this->setAvailability(false);
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
