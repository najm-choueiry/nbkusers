<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="financialDetails")
 */
class FinancialDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="sourceOfFunds", type="string", length=255)
     */
    private $sourceOfFunds;

    /**
     * @ORM\Column(name="currency", type="string", length=50)
     */
    private $currency;

    /**
     * @ORM\Column(name="monthlyBasicSalary", type="float")
     */
    private $monthlyBasicSalary;

    /**
     * @ORM\Column(name="monthlyAllowances", type="float")
     */
    private $monthlyAllowances;

    /**
     * @ORM\Column(name="additionalIncomeSources", type="string", length=255)
     */
    private $additionalIncomeSources;

    /**
     * @ORM\Column(name="totalEstimatedMonthlyIncome", type="float")
     */
    private $totalEstimatedMonthlyIncome;

    /**
     * @ORM\Column(name="isWealthInherited", type="boolean")
     */
    private $isWealthInherited;

    /**
     * @ORM\Column(name="expectedNumberOfTransactions", type="integer")
     */
    private $expectedNumberOfTransactions;

    /**
     * @ORM\Column(name="expectedValueOfTransactions", type="float")
     */
    private $expectedValueOfTransactions;

    /**
     * @ORM\Column(name="frequency", type="string", length=50)
     */
    private $frequency;
    /**
     * @ORM\Column(name="secondBankName",type="string", length=255, nullable=true)
     */
    private $secondBankName;

    /**
     * @ORM\Column(name="secondCountry",type="string", nullable=true)
     */
    private $secondCountry;

    /**
     * @ORM\Column(name="secondBankBalance",type="decimal", nullable=true)
     */
    private $secondBankBalance;

    /**
     * @ORM\Column(name="thirdBankName",type="string", nullable=true)
     */
    private $thirdBankName;

    /**
     * @ORM\Column(name="thirdAccountCountry",type="string",  nullable=true)
     */
    private $thirdAccountCountry;

    /**
     * @ORM\Column(name="thirdAccountBalance",type="decimal",  nullable=true)
     */
    private $thirdAccountBalance;

    /**
     * @ORM\Column(name="othersSourceOfFound",type="text", nullable=true)
     */
    private $othersSourceOfFound;

    /**
     * @ORM\Column(name="estimatedWealthAmount",type="decimal",  nullable=true)
     */
    private $estimatedWealthAmount;

    /**
     * @ORM\Column(name="sourcesOfWealth",type="text", nullable=true)
     */
    private $sourcesOfWealth;
   
       /**
     * @ORM\Column(name="incomeCategory", type="string", length=50, nullable=true)
     */
    private $incomeCategory;

    /**
     * @ORM\Column(name="otherAccountsAtBanks", type="boolean")
     */
    private $hasOtherAccounts;

    /**
     * @ORM\Column(name="bankName", type="string", length=255, nullable=true)
     */
    private $bankName;

    /**
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(name="accountBalance", type="float", nullable=true)
     */
    private $accountBalance;

    /**
     * @ORM\Column(name="natureOfRelation", type="string", length=50, nullable=true)
     */
    private $natureOfRelation;

    /**
     * @ORM\Column(name="purposeOfRelation", type="string", length=255, nullable=true)
     */
    private $purposeOfRelation;
    
     /**
     * @ORM\Column(name="selectIDType", type="string", length=255, nullable=true)
     */
    private $selectIDType;
     /**
     * @ORM\Column(name="frontImageID", type="string", length=50, nullable=true)
     */
    private $frontImageID;
     /**
     * @ORM\Column(name="backImageID", type="string", length=255, nullable=true)
     */
    private $backImageID;
     /**
     * @ORM\Column(name="realEstateTitle", type="string", length=255, nullable=true)
     */
    private $realEstateTitle;
     /**
     * @ORM\Column(name="accountStatement", type="string", length=255, nullable=true)
     */
    private $accountStatement;
        /**
     * @ORM\Column(name="otherDocument", type="string", length=255, nullable=true)
     */
    private $otherDocument;
        /**
     * @ORM\Column(name="employerLetter", type="string", length=255, nullable=true)
     */
    private $employerLetter;


    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="financialDetails")
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

    public function getSourceOfFunds(): ?string
    {
        return $this->sourceOfFunds;
    }

    public function setSourceOfFunds(string $sourceOfFunds): self
    {
        $this->sourceOfFunds = $sourceOfFunds;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getMonthlyBasicSalary(): ?float
    {
        return $this->monthlyBasicSalary;
    }

    public function setMonthlyBasicSalary(float $monthlyBasicSalary): self
    {
        $this->monthlyBasicSalary = $monthlyBasicSalary;

        return $this;
    }

    public function getMonthlyAllowances(): ?float
    {
        return $this->monthlyAllowances;
    }
    public function getOthersSourceOfFound(): ?string
    {
        return $this->othersSourceOfFound;
    }

    public function setOthersSourceOfFound(?string $othersSourceOfFound): self
    {
        $this->othersSourceOfFound = $othersSourceOfFound;

        return $this;
    }

    public function getEstimatedWealthAmount(): ?float
    {
        return $this->estimatedWealthAmount;
    }

    public function setEstimatedWealthAmount(?float $estimatedWealthAmount): self
    {
        $this->estimatedWealthAmount = $estimatedWealthAmount;

        return $this;
    }

    public function getSourcesOfWealth(): ?string
    {
        return $this->sourcesOfWealth;
    }

    public function setSourcesOfWealth(?string $sourcesOfWealth): self
    {
        $this->sourcesOfWealth = $sourcesOfWealth;

        return $this;
    }
    public function getIncomeCategory(): ?string
    {
        return $this->incomeCategory;
    }

    public function setIncomeCategory(?string $incomeCategory): self
    {
        $this->incomeCategory = $incomeCategory;

        return $this;
    }

public function getSecondBankName(): ?string
    {
        return $this->secondBankName;
    }

    public function setSecondBankName(?string $secondBankName): self
    {
        $this->secondBankName = $secondBankName;

        return $this;
    }

    public function getSecondCountry(): ?string
    {
        return $this->secondCountry;
    }

    public function setSecondCountry(?string $secondCountry): self
    {
        $this->secondCountry = $secondCountry;

        return $this;
    }

 public function getThirdBankName(): ?string
    {
        return $this->thirdBankName;
    }

    public function setThirdBankName(?string $thirdBankName): self
    {
        $this->thirdBankName = $thirdBankName;

        return $this;
    }

    public function getThirdAccountCountry(): ?string
    {
        return $this->thirdAccountCountry;
    }

    public function setThirdAccountCountry(?string $thirdAccountCountry): self
    {
        $this->thirdAccountCountry = $thirdAccountCountry;

        return $this;
    }

    public function setMonthlyAllowances(float $monthlyAllowances): self
    {
        $this->monthlyAllowances = $monthlyAllowances;

        return $this;
    }

    public function getAdditionalIncomeSourcesArray(): array
    {
        return $this->additionalIncomeSources ? explode(',', $this->additionalIncomeSources) : [];
    }

    public function setAdditionalIncomeSourcesArray(array $additionalIncomeSourcesArray): self
    {
        $this->additionalIncomeSources = implode(',', $additionalIncomeSourcesArray);
    
        return $this;
    }

    public function getTotalEstimatedMonthlyIncome(): ?float
    {
        return $this->totalEstimatedMonthlyIncome;
    }

    public function setTotalEstimatedMonthlyIncome(float $totalEstimatedMonthlyIncome): self
    {
        $this->totalEstimatedMonthlyIncome = $totalEstimatedMonthlyIncome;

        return $this;
    }

    public function getIsWealthInherited(): ?bool
    {
        return $this->isWealthInherited;
    }

    public function setIsWealthInherited(bool $isWealthInherited): self
    {
        $this->isWealthInherited = $isWealthInherited;

        return $this;
    }

    public function getExpectedNumberOfTransactions(): ?int
    {
        return $this->expectedNumberOfTransactions;
    }

    public function setExpectedNumberOfTransactions(int $expectedNumberOfTransactions): self
    {
        $this->expectedNumberOfTransactions = $expectedNumberOfTransactions;

        return $this;
    }

    public function getExpectedValueOfTransactions(): ?float
    {
        return $this->expectedValueOfTransactions;
    }

    public function setExpectedValueOfTransactions(float $expectedValueOfTransactions): self
    {
        $this->expectedValueOfTransactions = $expectedValueOfTransactions;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getHasOtherAccounts(): ?bool
    {
        return $this->hasOtherAccounts;
    }

    public function setHasOtherAccounts(bool $hasOtherAccounts): self
    {
        $this->hasOtherAccounts = $hasOtherAccounts;

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAccountBalance(): ?float
    {
        return $this->accountBalance;
    }

    public function setAccountBalance(?float $accountBalance): self
    {
        $this->accountBalance = $accountBalance;

        return $this;
    }

    public function getNatureOfRelation(): ?string
    {
        return $this->natureOfRelation;
    }

    public function setNatureOfRelation(?string $natureOfRelation): self
    {
        $this->natureOfRelation = $natureOfRelation;

        return $this;
    }

    public function getPurposeOfRelation(): ?string
    {
        return $this->purposeOfRelation;
    }
    public function setPurposeOfRelation(?string $purposeOfRelation): self
    {
        $this->purposeOfRelation = $purposeOfRelation;

        return $this;
    }

    public function setSelectIDType(?string $selectIDType): self
    {
        $this->selectIDType = $selectIDType;

        return $this;
    }
    public function getSelectIDType(): ?string
    {
        return $this->selectIDType;
    }
   

    public function setFrontImageID(?string $frontImageID): self
    {
        $this->frontImageID = $frontImageID;

        return $this;
    }
    public function getFrontImageID(): ?string
    {
        return $this->frontImageID;
    }

    public function setBackImageID(?string $backImageID): self
    {
        $this->backImageID = $backImageID;

        return $this;
    }
    public function getBackImageID(): ?string
    {
        return $this->backImageID;
    }

    public function setRealEstateTitle(?string $realEstateTitle): self
    {
        $this->realEstateTitle = $realEstateTitle;

        return $this;
    }
    public function getRealEstateTitle(): ?string
    {
        return $this->realEstateTitle;
    }
    
    public function setAccountStatement(?string $accountStatement): self
    {
        $this->accountStatement = $accountStatement;

        return $this;
    }
    public function getAccountStatement(): ?string
    {
        return $this->accountStatement;
    }

    public function setOtherDocument(?string $otherDocument): self
    {
        $this->otherDocument = $otherDocument;

        return $this;
    }
    public function getOtherDocument(): ?string
    {
        return $this->otherDocument;
    }

    public function setEmployerLetter(?string $employerLetter): self
    {
        $this->employerLetter = $employerLetter;

        return $this;
    }
    public function getEmployerLetter(): ?string
    {
        return $this->employerLetter;
    }
  

    public function getSecondBankBalance(): ?float
    {
        return $this->secondBankBalance;
    }

    public function setSecondBankBalance(?float $secondBankBalance): self
    {
        $this->secondBankBalance = $secondBankBalance;

        return $this;
    }


    public function getThirdAccountBalance(): ?float
    {
        return $this->thirdAccountBalance;
    }

    public function setThirdAccountBalance(?float $thirdAccountBalance): self
    {
        $this->thirdAccountBalance = $thirdAccountBalance;

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
