<?php

namespace App\Entity;

use App\Repository\DvdRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DvdRepository::class)
 */
class Dvd extends Document
{
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasBonus;

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(?\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getHasBonus(): ?bool
    {
        return $this->hasBonus;
    }

    public function setHasBonus(bool $hasBonus): self
    {
        $this->hasBonus = $hasBonus;

        return $this;
    }
}
