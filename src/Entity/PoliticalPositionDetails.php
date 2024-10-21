<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="politicalPositionDetails")
 */
class PoliticalPositionDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="politicalPosition", type="boolean")
     */
    private $politicalPosition;

    /**
     * @ORM\Column(name="currentOrPrevious", type="string", length=50, nullable=true)
     */
    private $currentPrevious;

    /**
     * @ORM\Column(name="yearOfRetirement", type="integer", nullable=true)
     */
    private $yearOfRetirement;

    /**
     * @ORM\Column(name="pepName", type="string", length=255, nullable=true)
     */
    private $pepName;

    /**
     * @ORM\Column(name="relationship", type="string", length=255, nullable=true)
     */
    private $relationship;

    /**
     * @ORM\Column(name="pepPosition", type="string", length=255, nullable=true)
     */
    private $pepPosition;



    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="politicalPositionDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @ORM\Column(name="created")
     */
    private \DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime();

    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoliticalPosition(): ?bool
    {
        return $this->politicalPosition;
    }

    public function setPoliticalPosition(bool $politicalPosition): self
    {
        $this->politicalPosition = $politicalPosition;

        return $this;
    }

    public function getCurrentPrevious(): ?string
    {
        return $this->currentPrevious;
    }

    public function setCurrentPrevious(?string $currentPrevious): self
    {
        $this->currentPrevious = $currentPrevious;

        return $this;
    }

    public function getYearOfRetirement(): ?int
    {
        return $this->yearOfRetirement;
    }

    public function setYearOfRetirement(?int $yearOfRetirement): self
    {
        $this->yearOfRetirement = $yearOfRetirement;

        return $this;
    }

    public function getPepName(): ?string
    {
        return $this->pepName;
    }

    public function setPepName(?string $pepName): self
    {
        $this->pepName = $pepName;

        return $this;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function setRelationship(?string $relationship): self
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function getPepPosition(): ?string
    {
        return $this->pepPosition;
    }

    public function setPepPosition(?string $pepPosition): self
    {
        $this->pepPosition = $pepPosition;

        return $this;
    }



    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
