<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="address")
 */
class Address
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
     * @ORM\Column(name="city", type="string", length=255)
     */
    
    private $city;
    /**
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @ORM\Column(name="building", type="string", length=255)
     */
    private $building;

    /**
     * @ORM\Column(name="floor", type="string", length=50)
     */
    private $floor;

    /**
     * @ORM\Column(name="apartment", type="string", length=50)
     */
    private $apartment;

    /**
     * @ORM\Column(name="houseTelephoneNumber", type="string", length=50)
     */
    private $houseTelephoneNumber;

    /**
     * @ORM\Column(name="internationalAddress", type="string", length=255)
     */
    private $internationalAddress;

    /**
     * @ORM\Column(name="intArea", type="string")
     */
    private $intArea;

    /**
     * @ORM\Column(name="intStreet", type="string")
     */
    private $intStreet;
    /**
     * @ORM\Column(name="intBuilding", type="string")
     */
    private $intBuilding;

    /**
     * @ORM\Column(name="intFloor", type="string")
     */
    private $intFloor;

    /**
     * @ORM\Column(name="intApartment", type="string")
     */
    private $intApartment;




    /**
     * @ORM\Column(name="internationalHouseTelephoneNumber", type="string", length=50)
     */
    private $internationalHouseTelephoneNumber;

    /**
     * @ORM\Column(name="internationalMobileNumber", type="string", length=50)
     */
    private $internationalMobileNumber;

    /**
     * @ORM\Column(name="alternateContactName", type="string", length=255)
     */
    private $alternateContactName;

    /**
     * @ORM\Column(name="alternateTelephoneNumber", type="string", length=50)
     */
    private $alternateTelephoneNumber;
    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="addresses")
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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }


    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    public function getHouseTelephoneNumber(): ?string
    {
        return $this->houseTelephoneNumber;
    }

    public function getInternationalAddress(): ?string
    {
        return $this->internationalAddress;
    }

    public function getInternationalHouseTelephoneNumber(): ?string
    {
        return $this->internationalHouseTelephoneNumber;
    }

    public function getInternationalMobileNumber(): ?string
    {
        return $this->internationalMobileNumber;
    }

    public function getAlternateContactName(): ?string
    {
        return $this->alternateContactName;
    }

    public function getAlternateTelephoneNumber(): ?string
    {
        return $this->alternateTelephoneNumber;
    }
    public function getIntArea(): ?string
    {
        return $this->intArea;
    }
    public function getIntStreet(): ?string
    {
        return $this->intStreet;
    }
    public function getIntBuilding(): ?string
    {
        return $this->intBuilding;
    }
    public function getIntFloor(): ?string
    {
        return $this->intFloor;
    }
    public function getIntApartment(): ?string
    {
        return $this->intApartment;
    }

    public function setIntApartment(string $intApartment): self
    {
        $this->intApartment = $intApartment;

        return $this;
    }
    public function setIntFloor(string $intFloor): self
    {
        $this->intFloor = $intFloor;

        return $this;
    }

    public function setIntBuilding(string $intBuilding): self
    {
        $this->intBuilding = $intBuilding;

        return $this;
    }
    public function setIntStreet(string $intStreet): self
    {
        $this->intStreet = $intStreet;

        return $this;
    }

    public function setIntArea(string $intArea): self
    {
        $this->intArea = $intArea;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }


    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function setBuilding(string $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function setFloor(string $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function setApartment(string $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function setHouseTelephoneNumber(string $houseTelephoneNumber): self
    {
        $this->houseTelephoneNumber = $houseTelephoneNumber;

        return $this;
    }

    public function setInternationalAddress(string $internationalAddress): self
    {
        $this->internationalAddress = $internationalAddress;

        return $this;
    }

    public function setInternationalHouseTelephoneNumber(string $internationalHouseTelephoneNumber): self
    {
        $this->internationalHouseTelephoneNumber = $internationalHouseTelephoneNumber;

        return $this;
    }

    public function setInternationalMobileNumber(string $internationalMobileNumber): self
    {
        $this->internationalMobileNumber = $internationalMobileNumber;

        return $this;
    }

    public function setAlternateContactName(string $alternateContactName): self
    {
        $this->alternateContactName = $alternateContactName;

        return $this;
    }

    public function setAlternateTelephoneNumber(string $alternateTelephoneNumber): self
    {
        $this->alternateTelephoneNumber = $alternateTelephoneNumber;

        return $this;
    }

    public function setDateTimeCreated(\DateTimeInterface $dateTimeCreated): self
    {
        $this->dateTimeCreated = $dateTimeCreated;

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
