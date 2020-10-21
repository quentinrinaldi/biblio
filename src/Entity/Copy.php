<?php

namespace App\Entity;

use App\Repository\CopyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CopyRepository::class)
 */
class Copy
{

    const PHYSICAL_STATE = [0 =>'AS_NEW', 1 => 'VERY_GOOD', 2 =>'GOOD', 3 => 'USED', 4 => 'DAMAGED'];
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
     * @Assert\Choice(choices=COPY::PHYSICAL_STATE, message="Choose a valid status.")
     */
    private $physicalState = self::PHYSICAL_STATE[0];

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
}
