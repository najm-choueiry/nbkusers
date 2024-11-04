<?php
// src/Repository/UsersRepository.php
namespace App\Repository;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UsersRepository
{
    private $entityManager;
    private $usersRepository;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->usersRepository = $entityManager->getRepository(Users::class);
    }
    public function createUser(array $userData, ?int $userId, ?int $branchId): ?Users
    {
        if (isset($userData['mothersName'])){
        $passportExpirationDateString = $userData['passportExpirationDate'] ?? '';
        $passportExpirationDate = new \DateTime($passportExpirationDateString->format('Y-m-d'));
        }
        if (is_null($userId)) {
            $user = new Users();
            $expirationDateNationalIdString = $userData['expirationDateNationalId'] ?? '';
            $expirationDateNationalId = new \DateTime($expirationDateNationalIdString);
            $dob = !empty($userData['dob']) ? \DateTime::createFromFormat('Y-m-d', $userData['dob']) : false;
            if ($dob) {
                $user->setDob($dob);
            }
        } else {
            $user = $this->usersRepository->find($userId);
            if (isset($userData['mothersName'])){
            $expirationDateNationalIdString = $userData['expirationDateNationalId'] ?? '';
            $expirationDateNationalIdString =   $expirationDateNationalIdString->format('Y-m-d');
            $expirationDateNationalId = (new \DateTime($expirationDateNationalIdString));
            $dob = !empty($userData['dob']->format('Y-m-d')) ? \DateTime::createFromFormat('Y-m-d', $userData['dob']->format('Y-m-d')) : false;
            if ($dob) {
                $user->setDob($dob);
            }
        }

        }
        $user->setFullName($userData['fullName'] ?? '');
        $user->setMobileNumb($userData['mobileNumb'] ?? '');
        $user->setEmail($userData['email'] ?? '');
        $branchUnits = [
            1 => 'sanayeh',
            2 => 'bhamdoun',
            3 => 'privatebank',
        ];
        // Set BranchUnit based on the branch ID, or default to an empty string if no match is found
        $user->setBranchUnit($branchUnits[$branchId] ?? '');
        $user->setBranchId((int)($userData['branchId'] ?? 0));
        if (isset($userData['mothersName'])){
        $user->setMothersName($userData['mothersName'] ?? '');
        $user->setGender($userData['gender'] ?? '');
        $user->setPlaceOfBirth($userData['placeOfBirth'] ?? '');
        $user->setCountryOfOrigin($userData['countryOfOrigin'] ?? '');
        $user->setNationality($userData['nationality'] ?? '');
        $user->setNationalId($userData['nationalId'] ?? '');
        $user->setExpirationDateNationalId($expirationDateNationalId);
        $user->setRegisterPlaceNo($userData['registerPlaceAndNo'] ?? '');
        $user->setMaritalStatus($userData['maritalStatus'] ?? '');
        $user->setPassportNumber($userData['passportNumber'] ?? '');
        $user->setPlaceOfIssuePassport($userData['placeOfIssuePassport'] ?? '');
        $user->setExpirationDatePassport($passportExpirationDate);
        $user->setOtherNationalities($userData['otherNationalities'] ?? '');
        $user->setStatusInLebanon($userData['statusInLebanon'] ?? '');
        $user->setOtherCountriesTaxResidence($userData['otherCountriesOfTaxResidence'] ?? '');
        $user->setTaxResidencyIdNumber($userData['taxResidencyIdNumber'] ?? '');
        $user->setSpouseName($userData['spouseName'] ?? '');
        $user->setSpouseProfession($userData['spouseProfession'] ?? '');
        $user->setNoOfChildren((int)($userData['noOfChildren'] ?? 0));
        $user->setRegisterNumber((int)($userData['registerNumber'] ?? 0));
        }
        return $user;
    }

    public function createExistingUser(array $userData): ?Users
    {
        $user = new Users();
        $user->setFullName($userData['fullName'] ?? '');
        $user->setMobileNumb($userData['mobileNumb'] ?? '');
        $user->setEmail($userData['email'] ?? '');
        $user->setBranchUnit($userData['branchUnit'] ?? '');
        $user->setBranchId((int)($userData['branchId'] ?? 0));
        return $user;
    }
}
