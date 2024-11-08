<?php

// src/Entity/Accounts.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 * @ORM\HasLifecycleCallbacks()  // Enable lifecycle callbacks
 */
class Accounts
{
	public static $UsersStatusArray = array(0 => "FILTER BY ROLE", "ROLE_ADMIN" => "ROLE_ADMIN", "ROLE_USER" => "ROLE_USER");

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=100, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", length=20, unique=true, nullable=true)
	 */
	private $phone;

	/**
	 * @ORM\Column(type="string", length=30)
	 */
	private $roles;

	/**
	 * @ORM\Column(type="string", name="authCode" , nullable=true)
	 */
	private $authCode;

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
	 */
	private $lastLogin;




	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setUsername(string $username): self
	{
		$this->username = $username;

		return $this;
	}

	public function getRoles(): array
	{
		$roles = [];

		// You may want to check for multiple roles and split them from the string if needed
		$roles[] = $this->roles;

		return $roles;
	}

	public function setRoles(array $roles)
	{
		$this->roles = implode(',', $roles);  // Convert array to comma-separated string
		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt(): ?string
	{
		return null;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
	}


	public function isEmailAuthEnabled(): bool
	{
		return true;
	}

	public function getEmailAuthRecipient(): string
	{
		return $this->email;
	}

	public function getEmailAuthCode(): string
	{
		return $this->authCode;
	}

	public function setEmailAuthCode(string $authCode): void
	{
		$this->authCode = $authCode;
	}

	public function sendAuthCode($sms)
	{
		return $sms->send($this->authCode, $this->phone) == "0";
	}

	public function getLastLogin()
	{
		return $this->lastLogin;
	}

	public function setLastLogin(?\DateTimeInterface $lastLogin): self
	{
		$this->lastLogin = $lastLogin;
		return $this;
	}
}
