<?php

// src/Repository/PoliticalPositionDetailsRepository.php

namespace App\Repository;

use App\Entity\PoliticalPositionDetails;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PoliticalPositionDetailsRepository
{
    private $entityManager;
    private $politicalPositionDetailsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->politicalPositionDetailsRepository = $entityManager->getRepository(PoliticalPositionDetails::class);
    }
    public function createPoliticalPosition(array $userData, ?int $userId): ?PoliticalPositionDetails
    {
        $yearOfRetirementString = $userData['yearOfRetirement'] ?? '';
        $yearOfRetirement = $yearOfRetirementString !== '' ? (int)$yearOfRetirementString : null;

        // Set properties for PoliticalPositionDetails entity
        if (is_null($userId)) {
            $politicalPosition = new PoliticalPositionDetails();
        } else {
            $politicalPositio = $this->politicalPositionDetailsRepository->findBy(['user_id' => $userId]);
            $politicalPosition = $politicalPositio[0];
        }
        $politicalPosition->setPoliticalPosition($userData['politicalPosition'] ?? '');
        $politicalPosition->setCurrentPrevious($userData['currentOrPrevious'] ?? '');
        $politicalPosition->setYearOfRetirement($yearOfRetirement);
        $politicalPosition->setPepName($userData['pepName'] ?? '');
        $politicalPosition->setRelationship($userData['relationship'] ?? '');
        $politicalPosition->setPepPosition($userData['pepPosition'] ?? '');



        return $politicalPosition;
    }
}
