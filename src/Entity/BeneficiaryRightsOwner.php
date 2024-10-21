<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="broDetails")
 */
class BeneficiaryRightsOwner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="customerSameAsBeneficiary", type="boolean")
     */
    private $customerSameAsBeneficiary;

    /**
     * @ORM\Column(name="broNationality", type="string", length=255, nullable=true)
     */
    private $broNationality;

    /**
     * @ORM\Column(name="beneficiaryName", type="string", length=255, nullable=true)
     */
    private $beneficiaryName;

    /**
     * @ORM\Column(name="relationship", type="string", length=255, nullable=true)
     */
    private $relationship;

    /**
     * @ORM\Column(name="broCivilIdNumber", type="string", length=50, nullable=true)
     */
    private $broCivilIdNumber;

    /**
     * @ORM\Column(name="expirationDate", type="date", nullable=true)
     */
    private $expirationDate;

    /**
     * @ORM\Column(name="reasonOfBro", type="string", length=255, nullable=true)
     */
    private $reasonOfBro;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @ORM\Column(name="incomeWealthDetails", type="string", length=255, nullable=true)
     */
    private $incomeWealthDetails;

    /**
     * @ORM\Column(name="created")
     */
    private \DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime();

    }
    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="beneficiaryRightsOwners")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerSameAsBeneficiary(): ?bool
    {
        return $this->customerSameAsBeneficiary;
    }

    public function getBroNationality(): ?string
    {
        return $this->broNationality;
    }

    public function getBeneficiaryName(): ?string
    {
        return $this->beneficiaryName;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getBroCivilIdNumber(): ?string
    {
        return $this->broCivilIdNumber;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function getReasonOfBro(): ?string
    {
        return $this->reasonOfBro;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function getIncomeWealthDetails(): ?string
    {
        return $this->incomeWealthDetails;
    }

    public function setCustomerSameAsBeneficiary(bool $customerSameAsBeneficiary): self
    {
        $this->customerSameAsBeneficiary = $customerSameAsBeneficiary;

        return $this;
    }

    public function setBroNationality(string $broNationality): self
    {
        $this->broNationality = $broNationality;

        return $this;
    }

    public function setBeneficiaryName(string $beneficiaryName): self
    {
        $this->beneficiaryName = $beneficiaryName;

        return $this;
    }

    public function setRelationship(string $relationship): self
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function setBroCivilIdNumber(string $broCivilIdNumber): self
    {
        $this->broCivilIdNumber = $broCivilIdNumber;

        return $this;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function setReasonOfBro(string $reasonOfBro): self
    {
        $this->reasonOfBro = $reasonOfBro;

        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function setIncomeWealthDetails(string $incomeWealthDetails): self
    {
        $this->incomeWealthDetails = $incomeWealthDetails;

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
