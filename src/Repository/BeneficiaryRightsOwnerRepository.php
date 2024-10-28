<?php

// src/Repository/BeneficiaryRightsOwnerRepository.php

namespace App\Repository;

use App\Entity\BeneficiaryRightsOwner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BeneficiaryRightsOwnerRepository
{
    private $entityManager;
    private $beneficiaryRightsOwnerRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->beneficiaryRightsOwnerRepository = $entityManager->getRepository(BeneficiaryRightsOwner::class);
    }

    public function createBeneficiary(array $userData, ?int $userId): ?BeneficiaryRightsOwner
    {
        $expirationDateString = $userData['expirationDate'] ?? '';
        $expirationDate = new \DateTime($expirationDateString->format('Y-m-d'));
        if (is_null($userId)) {
            $beneficiary = new BeneficiaryRightsOwner();
        } else {
            $beneficiar = $this->beneficiaryRightsOwnerRepository->findBy(['user_id' => $userId]);
            $beneficiary = $beneficiar[0];
        }
        $beneficiary->setCustomerSameAsBeneficiary($userData['customerSameAsBeneficiary'] ?? false);
        $beneficiary->setBroNationality($userData['broNationality'] ?? '');
        $beneficiary->setBeneficiaryName($userData['beneficiaryName'] ?? '');
        $beneficiary->setRelationship($userData['relationship'] ?? '');
        $beneficiary->setBroCivilIdNumber($userData['broCivilIdNumber'] ?? '');
        $beneficiary->setExpirationDate($expirationDate);
        $beneficiary->setReasonOfBro($userData['reasonOfBro'] ?? '');
        $beneficiary->setAddress($userData['address'] ?? '');
        $beneficiary->setProfession($userData['profession'] ?? '');
        $beneficiary->setIncomeWealthDetails($userData['incomeWealthDetails'] ?? '');
        return $beneficiary;
    }
}
