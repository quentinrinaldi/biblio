<?php

namespace App\Entity;

use App\Repository\ArtistInvolvementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArtistInvolvementRepository::class)
 */
class ArtistInvolvement
{
    public const TYPE_AUTHOR = 'author';
    public const TYPE_TRANSLATOR = 'translator';
    public const TYPE_PREFACE = 'preface';
    public const TYPE_ARRAY = [self::TYPE_AUTHOR, self::TYPE_TRANSLATOR, self::TYPE_PREFACE];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices="self::TYPE_ARRAY", message="Choose a valid role")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class, inversedBy="artistInvolvements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $artist;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="artistInvolvements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document;

    public function __toString()
    {
        return "{$this->getArtist()->__toString()} ({$this->getType()})";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
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
}
