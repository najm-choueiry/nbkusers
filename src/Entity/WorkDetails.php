<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="workDetails")
 */
class WorkDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;
    
    /**
     * @ORM\Column(name="profession", type="string", length=255)
     */
    private $profession;

    /**
     * @ORM\Column(name="jobTitle", type="string", length=255)
     */
    private $jobTitle;

    /**
     * @ORM\Column(name="publicSector", type="string", length=50)
     */
    private $publicSector;

    /**
     * @ORM\Column(name="activitySector", type="string", length=50)
     */
    private $activitySector;

    /**
     * @ORM\Column(name="entityName", type="string", length=255)
     */
    private $entityName;

    /**
     * @ORM\Column(name="educationLevel", type="string", length=50)
     */
    private $educationLevel;

    /**
     * @ORM\Column(name="workAddress", type="string", length=255)
     */
    private $workAddress;

    /**
     * @ORM\Column(name="workTelephoneNumber", type="string", length=50)
     */
    private $workTelephoneNumber;

    /**
     * @ORM\Column(name="placeOfWorkListed", type="boolean")
     */
    private $placeOfWorkListed;

    /**
     * @ORM\Column(name="grade", type="string", length=50, nullable=true)
     */
    private $grade;
    /**
     * @ORM\Column(name="created")
     */
    private \DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime();

    }

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="workDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getPublicSector(): ?string
    {
        return $this->publicSector;
    }

    public function getActivitySector(): ?string
    {
        return $this->activitySector;
    }

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function getEducationLevel(): ?string
    {
        return $this->educationLevel;
    }

    public function getWorkAddress(): ?string
    {
        return $this->workAddress;
    }

    public function getWorkTelephoneNumber(): ?string
    {
        return $this->workTelephoneNumber;
    }

    public function getPlaceOfWorkListed(): ?bool
    {
        return $this->placeOfWorkListed;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function setPublicSector(string $publicSector): self
    {
        $this->publicSector = $publicSector;

        return $this;
    }

    public function setActivitySector(string $activitySector): self
    {
        $this->activitySector = $activitySector;

        return $this;
    }

    public function setEntityName(string $entityName): self
    {
        $this->entityName = $entityName;

        return $this;
    }

    public function setEducationLevel(string $educationLevel): self
    {
        $this->educationLevel = $educationLevel;

        return $this;
    }

    public function setWorkAddress(string $workAddress): self
    {
        $this->workAddress = $workAddress;

        return $this;
    }

    public function setWorkTelephoneNumber(string $workTelephoneNumber): self
    {
        $this->workTelephoneNumber = $workTelephoneNumber;

        return $this;
    }

    public function setPlaceOfWorkListed(bool $placeOfWorkListed): self
    {
        $this->placeOfWorkListed = $placeOfWorkListed;

        return $this;
    }

    public function setGrade(string $grade): self
    {
        $this->grade = $grade;

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
