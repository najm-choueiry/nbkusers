<?php
// src/Repository/FinancialDetailsRepository.php

namespace App\Repository;

use App\Entity\FinancialDetails;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FinancialDetailsRepository
{
	private $entityManager;
	private $financialDetailsRepository;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->financialDetailsRepository = $entityManager->getRepository(FinancialDetails::class);
	}
	public function createFinancialDetails(array $userData, $modifiedName, $mobileNumbDB, $mobileNumb, $fullNameDB, $fullName, $frontimageDB, $backimageDB, $realEstateTitleDB, $accountStatementDB, $otherDocumentDB, $employerLetterDB, $userId, $imageFrontDB, $imageBackDB, $imageRealEStateDB, $imageFrontaccoountStatDB, $imageotherdocDB, $imageEmployerLetterDB): ?FinancialDetails
	{
		if (is_null($userId)) {
			$financialDetails = new FinancialDetails();
		} else {
			$financialDetail = $this->financialDetailsRepository->findBy(['user_id' => $userId]);
			$financialDetails = $financialDetail[0];
		}
		$financialDetails->setSourceOfFunds($userData['sourceOfFunds'] ?? '');
		$financialDetails->setCurrency($userData['currency'] ?? '');
		$financialDetails->setMonthlyBasicSalary($userData['monthlyBasicSalary'] ?? 0.0);
		$financialDetails->setMonthlyAllowances($userData['monthlyAllowances'] ?? 0.0);
		$additionalIncomeSources = $userData['additionalIncomeSources'] ?? [];
		if (is_string($additionalIncomeSources)) {
			$additionalIncomeSources = [$additionalIncomeSources];
		}
		$financialDetails->setAdditionalIncomeSourcesArray($additionalIncomeSources);
		$financialDetails->setTotalEstimatedMonthlyIncome((float)($userData['totalEstimatedMonthlyIncome'] ?? 0.0));
		$financialDetails->setIsWealthInherited($userData['isWealthInherited'] ?? false);
		$financialDetails->setExpectedNumberOfTransactions((int)($userData['expectedNumberOfTransactions'] ?? 0));
		$financialDetails->setExpectedValueOfTransactions((float)($userData['expectedValueOfTransactions'] ?? 0.0));
		$financialDetails->setFrequency($userData['frequency'] ?? '');
		$financialDetails->setHasOtherAccounts($userData['hasOtherAccounts'] ?? false);
		$financialDetails->setBankName($userData['bankName'] ?? '');
		$financialDetails->setCountry($userData['country'] ?? '');
		$financialDetails->setAccountBalance((float)($userData['accountBalance'] ?? 0.0));
		$financialDetails->setNatureOfRelation($userData['natureOfRelation'] ?? '');
		$financialDetails->setPurposeOfRelation($userData['purposeOfRelation'] ?? '');
		$financialDetails->setOthersSourceOfFound($userData['othersSourceOfFound'] ?? '');
		$financialDetails->setEstimatedWealthAmount($userData['estimatedWealthAmount'] ?? '');
		$financialDetails->setSourcesOfWealth($userData['sourcesOfWealth'] ?? '');
		$financialDetails->setIncomeCategory($userData['incomeCategory'] ?? '');
		$financialDetails->setSecondBankName($userData['bankName2'] ?? '');
		$financialDetails->setSecondCountry($userData['country2'] ?? '');
		$financialDetails->setSecondBankBalance((float)($userData['accountBalance2'] ?? 0.0));
		$financialDetails->setThirdBankName($userData['bankName3'] ?? '');
		$financialDetails->setThirdAccountCountry($userData['country3'] ?? '');
		$financialDetails->setThirdAccountBalance((float)($userData['accountBalance3'] ?? 0.0));

		if ($fullName !== $fullNameDB || $mobileNumb !== $mobileNumbDB) {
			$newIdentifier = $modifiedName . "-" .  $mobileNumb;
			//frontimage
			if (!is_null($frontimageDB) || !empty($frontimageDB)) {
				$baseNameFront = basename($frontimageDB);
				$newImagePathFront = "imageUser/{$newIdentifier}/$baseNameFront";
				$financialDetails->setFrontImageID('/' . $newImagePathFront);
			}
			//backimage
			if (!is_null($backimageDB) || !empty($backimageDB)) {
				$baseName = basename($backimageDB);
				$newImagePath = "imageUser/{$newIdentifier}/$baseName";
				$financialDetails->setBackImageID('/' . $newImagePath);
			}
			//imageRealEStateDB
			if (!is_null($realEstateTitleDB) || !empty($realEstateTitleDB)) {
				$baseName = basename($realEstateTitleDB);
				$newImagePath = "imageUser/{$newIdentifier}/$baseName";
				$financialDetails->setRealEstateTitle('/' . $newImagePath);
			}
			//AccountStatement
			if (!is_null($accountStatementDB) || !empty($accountStatementDB)) {
				$baseName = basename($accountStatementDB);
				$newImagePath = "imageUser/{$newIdentifier}/$baseName";
				$financialDetails->setAccountStatement('/' . $newImagePath);
			}
			//otherDocument
			if (!is_null($otherDocumentDB) || !empty($otherDocumentDB)) {
				$baseName = basename($otherDocumentDB);
				$newImagePath = "imageUser/{$newIdentifier}/$baseName";
				$financialDetails->setOtherDocument('/' . $newImagePath);
			}
			//employerLetterDB
			if (!is_null($employerLetterDB) || !empty($employerLetterDB)) {
				$baseName = basename($employerLetterDB);
				$newImagePath = "imageUser/{$newIdentifier}/$baseName";
				$financialDetails->setEmployerLetter('/' . $newImagePath);
			}
		}
		if (!is_null($imageFrontDB)) {
			$financialDetails->setFrontImageID('/' . $imageFrontDB);
		}
		$financialDetails->setSelectIDType($userData['selectIDType'] ?? '');

		if (!is_null($imageBackDB)) {
			$financialDetails->setBackImageID('/' . $imageBackDB);
		}
		if (!is_null($imageRealEStateDB)) {
			$financialDetails->setRealEstateTitle('/' . $imageRealEStateDB);
		}
		if (!is_null($imageFrontaccoountStatDB)) {
			$financialDetails->setAccountStatement('/' . $imageFrontaccoountStatDB);
		}
		if (!is_null($imageotherdocDB)) {
			$financialDetails->setOtherDocument('/' . $imageotherdocDB);
		}
		if (!is_null($imageEmployerLetterDB)) {
			$financialDetails->setEmployerLetter('/' . $imageEmployerLetterDB);
		}
		return $financialDetails;
	}
}
