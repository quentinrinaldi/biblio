<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 *
 */
class Book extends Document
{

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $isbn;

    /**
     * @ORM\Column(type="integer")
     */
    private $pagesCount;

    public function getPagesCount(): ?int
    {
        return $this->pagesCount;
    }

    public function setPagesCount(int $pagesCount): self
    {
        $this->pagesCount = $pagesCount;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }
}