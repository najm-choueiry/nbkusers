<?php

// src/Repository/WorkDetailsRepository.php

namespace App\Repository;

use App\Entity\WorkDetails;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WorkDetailsRepository
{
    private $entityManager;
    private $WorkDetailsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->WorkDetailsRepository = $entityManager->getRepository(WorkDetails::class);
    }

    public function createWorkDetails(array $userData,?int $userId): ?WorkDetails
    
    {
        // Set properties for WorkDetails entity
        if (is_null($userId)) {
            $workDetails = new WorkDetails();
        }
        else{
            $workDetail = $this->WorkDetailsRepository->findBy(['user_id' => $userId]);
            $workDetails = $workDetail[0]; 
        }
        $workDetails->setProfession($userData['profession'] ?? '');
        $workDetails->setJobTitle($userData['jobTitle'] ?? '');
        $workDetails->setPublicSector($userData['publicSector'] ?? '');
        $workDetails->setActivitySector($userData['activitySector'] ?? '');
        $workDetails->setEntityName($userData['entityName'] ?? '');
        $workDetails->setEducationLevel($userData['educationLevel'] ?? '');
        $workDetails->setWorkAddress($userData['workAddress'] ?? '');
        $workDetails->setWorkTelephoneNumber($userData['workTelephoneNumber'] ?? '');
        $workDetails->setPlaceOfWorkListed($userData['placeOfWorkListed'] ?? false);
        $workDetails->setGrade($userData['grade'] ?? '');


        return $workDetails;
    }
}
