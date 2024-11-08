<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\BeneficiaryRightsOwner;
use App\Entity\FinancialDetails;
use App\Entity\PoliticalPositionDetails;
use App\Entity\Users;
use App\Entity\WorkDetails;
use App\Utils\Helper;
use App\Entity\Emails;
use App\Form\EditType;
use App\Repository\AddressRepository;
use App\Repository\BeneficiaryRightsOwnerRepository;
use App\Repository\FinancialDetailsRepository;
use App\Repository\PoliticalPositionDetailsRepository;
use App\Repository\UsersRepository;
use App\Repository\WorkDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DefaultController extends AbstractController
{
	private $entityManager;
	private $helper;
	private $client;

	public function __construct(EntityManagerInterface $entityManager, Helper $helper, HttpClientInterface $client)
	{
		$this->entityManager = $entityManager;
		$this->helper = $helper;
		$this->client = $client;
	}

	#[Route('/', name: 'app_nbk_users')]
	public function index(Request $request, PaginatorInterface $paginator): Response
	{
		$sortField = $request->query->get('sort', 'u.id');
		$sortDirection = $request->query->get('direction', 'DESC');
		$userRepository = $this->entityManager->getRepository(Users::class);
		$queryBuilder = $userRepository->createQueryBuilder('u')
			->leftJoin('u.addresses', 'a')
			->leftJoin('u.workDetails', 'w')
			->leftJoin('u.financialDetails', 'f')
			->leftJoin('u.politicalPositionDetails', 'p')
			->leftJoin('u.beneficiaryRightsOwners', 'b')
			->addSelect('a', 'w', 'f', 'p', 'b')
			->orderBy($sortField, $sortDirection);
		$query = $queryBuilder->getQuery();

		$pagination = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			15
		);
		return $this->render('nbkusers/user_list.html.twig', [
			'pagination' => $pagination,
		]);
	}
	#[Route('/submit-form/{id}', name: 'submit_form', methods: ['GET'])]
	public function submitForm($id)
	{
		$emailsOfUser = $this->entityManager->getRepository(Emails::class)->findBy([
			'user_id' => $id,
			'identifier' => 'Branch'
		]);
		foreach ($emailsOfUser as $email) {
			$email->setStatus('TobeResent');
		}
		$this->entityManager->flush();

		return new Response('Email sent successfully.');
	}
	#[Route('/userInfo/{id}', name: 'user_info', methods: ['GET'])]
	public function userInfo(int $id): Response
	{
		$userRepository = $this->entityManager->getRepository(Users::class);
		$user = $userRepository->createQueryBuilder('u')
			->leftJoin('u.addresses', 'a')
			->leftJoin('u.workDetails', 'w')
			->leftJoin('u.financialDetails', 'f')
			->leftJoin('u.politicalPositionDetails', 'p')
			->leftJoin('u.beneficiaryRightsOwners', 'b')
			->addSelect('a') // Ensure the associated collections are selected
			->addSelect('w')
			->addSelect('f')
			->addSelect('p')
			->addSelect('b')
			->where('u.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getResult();
		return $this->render('nbkusers/userInfo.html.twig', [
			'pagination' => $user,
		]);
	}
	#[Route('/nbk/editInfo/{userId}', name: 'edit_info')]
	public function editInfo($userId, UsersRepository $usersRepository, AddressRepository $addressRepository, WorkDetailsRepository $workDetailsRepository, BeneficiaryRightsOwnerRepository $beneficiaryRepository, PoliticalPositionDetailsRepository $politicalPositionRepository, FinancialDetailsRepository $financialRepository, Request $request): Response
	{
		$userRepository = $this->entityManager->getRepository(Users::class);
		$user = $userRepository->createQueryBuilder('u')
			->leftJoin('u.addresses', 'a')
			->leftJoin('u.workDetails', 'w')
			->leftJoin('u.financialDetails', 'f')
			->leftJoin('u.politicalPositionDetails', 'p')
			->leftJoin('u.beneficiaryRightsOwners', 'b')
			->addSelect('a')
			->addSelect('w')
			->addSelect('f')
			->addSelect('p')
			->addSelect('b')
			->where('u.id = :id')
			->setParameter('id', $userId)
			->getQuery()
			->getResult();
		$form = $this->createForm(EditType::class, null, [
			'user_class' => Users::class,
			'address_class' => Address::class,
			'WorkDetails_class' => WorkDetails::class,
			'broDetails_class' => BeneficiaryRightsOwner::class,
			'PoliticalPosition_class' => PoliticalPositionDetails::class,
			'FinancialDetails_class' => FinancialDetails::class,
			'user_id' => $userId,
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$formData = $form->getData();
			if ($user[0]->getMothersName()) {

				$data = [
					'user' => [
						'fullName' => $formData['FullName'],
						'mobileNumb' => $formData['MobileNumb'],
						'email' => $formData['Email'],
						'branchId' => $formData['BranchId'],
						'mothersName' => $formData['MotherName'],
						'gender' => $formData['Gender'],
						'dob' => $formData['Dob'],
						'placeOfBirth' => $formData['PlaceOfBirth'],
						'countryOfOrigin' => $formData['CountryofOrigin'],
						'nationality' => $formData['Nationality'],
						'nationalId' => $formData['NationalID'],
						'expirationDateNationalId' => $formData['ExpirationNationalIDDate'],
						'registerPlaceAndNo' => $formData['RegisterPlaceNo'],
						'registerNumber' => $formData['RegisterNumber'],
						'maritalStatus' => $formData['MaritalStatus'],
						'passportNumber' => $formData['PassportNumber'],
						'placeOfIssuePassport' => $formData['PlaceofIssuePassport'],
						'passportExpirationDate' => $formData['ExpirationDatePassport'],
						'otherNationalities' => $formData['OtherNationalities'],
						'statusInLebanon' => $formData['StatusinLebanon'],
						'otherCountriesOfTaxResidence' => $formData['otherCountriesTaxResidence'],
						'taxResidencyIdNumber' => $formData['TaxResidencyIdNumber'],
						'spouseName' => $formData['SpouseName'],
						'spouseProfession' => $formData['SpouseProfession'],
						'noOfChildren' => $formData['NoofChildren'],
					],
					'address' => [
						'city' => $formData['city'],
						'street' => $formData['street'],
						'building' => $formData['building'],
						'floor' => $formData['floor'],
						'apartment' => $formData['apartment'],
						'houseTelephoneNumber' => $formData['HouseTelNO'],
						'internationalAddress' => $formData['InternationalAddress'],
						'internationalHouseTelephoneNumber' => $formData['internationalHouseTelephoneNumber'],
						'internationalMobileNumber' => $formData['internationalMobileNumber'],
						'alternateContactName' => $formData['alternateContactName'],
						'alternateTelephoneNumber' => $formData['alternateTelephoneNumber'],
						'intArea' => $formData['intArea'],
						'intStreet' => $formData['intStreet'],
						'intBuilding' => $formData['intBuilding'],
						'intFloor' => $formData['intFloor'],
						'intApartment' => $formData['intAppartment'],
					],
					'workDetails' => [
						'profession' => $formData['profession'],
						'jobTitle' => $formData['jobTitle'],
						'publicSector' => $formData['publicSector'],
						'activitySector' => $formData['activitySector'],
						'entityName' => $formData['entityName'],
						'educationLevel' => $formData['educationLevel'],
						'workAddress' => $formData['WorkAddress'],
						'workTelephoneNumber' => $formData['WorkTelNo'],
						'placeOfWorkListed' => $formData['ISListed'],
						'grade' => $formData['grade'],
					],
					'beneficiaryRightsOwner' => [
						'customerSameAsBeneficiary' => $formData['CustomerSameAsBeneficiary'],
						'broNationality' => $formData['broNationality'],
						'beneficiaryName' => $formData['BeneficiaryName'],
						'relationship' => $formData['relationship'],
						'broCivilIdNumber' => $formData['broCivilIdNumber'],
						'expirationDate' => $formData['broexpirationDate'],
						'reasonOfBro' => $formData['reasonOfBro'],
						'address' => $formData['broaddress'],
						'profession' => $formData['broprofession'],
						'incomeWealthDetails' => $formData['incomeWealthDetails'],
					],
					'politicalPositionDetails' => [
						'politicalPosition' => $formData['politicalPosition'],
						'currentOrPrevious' => $formData['currentPrevious'],
						'yearOfRetirement' => $formData['yearOfRetirement'],
						'pepName' => $formData['pepname'],
						'relationship' => $formData['peprelationship'],
						'pepPosition' => $formData['pepposition'],
					],
					'financialDetails' => [
						'sourceOfFunds' => $formData['sourceOfFunds'],
						'currency' => $formData['financelcurrency'],
						'monthlyBasicSalary' => $formData['monthlyBasicSalary'],
						'monthlyAllowances' => $formData['monthlyAllowances'],
						'additionalIncomeSources' => $formData['AdditionalIncomeSourcesArray'],
						'othersSourceOfFound' => $formData['othersSourceOfFound'],
						'totalEstimatedMonthlyIncome' => $formData['TotalEstimatedMonthlyIncome'],
						'estimatedWealthAmount' => $formData['estimatedWealthAmount'],
						'isWealthInherited' => $formData['isWealthInherited'],
						'sourcesOfWealth' => $formData['sourcesOfWealth'],
						'incomeCategory' => $formData['IncomeCategory'],
						'expectedNumberOfTransactions' => $formData['expectedNumberOfTransactions'],
						'expectedValueOfTransactions' => $formData['expectedValueOfTransactions'],
						'frequency' => $formData['finanfrequency'],
						'hasOtherAccounts' => $formData['otherAccountsAtBanks'],
						'bankName' => $formData['bankNameone'],
						'country' => $formData['Countryone'],
						'accountBalance' => $formData['AccountBalanceOne'],
						'bankName2' => $formData['secondBankName'],
						'country2' => $formData['secondCountry'],
						'accountBalance2' => $formData['secondBankBalance'],
						'bankName3' => $formData['thirdBankName'],
						'country3' => $formData['thirdAccountCountry'],
						'accountBalance3' => $formData['thirdAccountBalance'],
						'natureOfRelation' => $formData['natureOfRelation'],
						'purposeOfRelation' => $formData['purposeOfRelation'],
						'selectIDType' => $formData['selectIDType'],
						'frontImageID' => $formData['frontImageID'] ?? null,
						'backImageID' => $formData['backImageID'] ?? null,
						'realEstateTitle' => $formData['realEstateTitle'] ?? null,
						'accountStatement' => $formData['accountStatement'] ?? null,
						'otherDocument' => $formData['otherDocument'] ?? null,
						'employerLetter' => $formData['employerLetter'] ?? null,
					]
				];
			} else {
				$data = [
					'user' => [
						'fullName' => $formData['FullName'],
						'mobileNumb' => $formData['MobileNumb'],
						'email' => $formData['Email'],
						'branchId' => $formData['BranchId']
					]
				];
			}
			if ($data === null) {
				return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
			}
			$branchId = $data['user']['branchId'];
			$dataUserDB = $this->entityManager->getRepository(Users::class)->find($userId);
			$mobileNumbDB = $dataUserDB->getMobileNumb();
			$fullNameDB =  $dataUserDB->getFullName();
			$dataFinancialDetailsDB = $this->entityManager->getRepository(FinancialDetails::class)->findBy(['user_id' => $userId]);
			if ($user[0]->getMothersName()) {
				$financialDetailDB = $dataFinancialDetailsDB[0];
				$frontimageDB = $financialDetailDB->getFrontImageID();
			}
			$userEmail = $data['user']['email'];
			if (!$this->helper->isValidEmail($userEmail)) {
				return new JsonResponse(['error' => 'Invalid email address'], Response::HTTP_BAD_REQUEST);
			}
			$user = $usersRepository->createUser($data['user'], $userId, $branchId);
			if (!$user) {
				return new JsonResponse(['error' => 'Failed to create user'], Response::HTTP_BAD_REQUEST);
			}
			$modifiedName = '';
			$fullName = $data['user']['fullName'];
			$mobileNumb = $data['user']['mobileNumb'];
			for ($i = 0; $i < strlen($fullName); $i++) {
				$char = $fullName[$i];

				if (ctype_alpha($char)) {
					$modifiedName .= $char;
				} else {
					$i++;
				}
			}
			if ($user->getMothersName()) {
				$frontImageID = $data['financialDetails']['frontImageID'];
				$backImageID = $data['financialDetails']['backImageID'];
				$realStateImage = $data['financialDetails']['realEstateTitle'];
				$otherDocumentImage = $data['financialDetails']['otherDocument'];
				$accountStatementImage = $data['financialDetails']['accountStatement'];
				$employeeLetterImage = $data['financialDetails']['employerLetter'];

				$ModifiedNameDB = explode('-', explode('/', $frontimageDB)[2])[0];

				$this->helper->unsetImagesFromData($data['financialDetails']);

				$address = $addressRepository->createAddress($data['address'] ?? [], $userId);
				if ($address) {
					$address->setUser($user);
				}
				$workDetails = $workDetailsRepository->createWorkDetails($data['workDetails'] ?? [], $userId);
				if ($workDetails) {
					$workDetails->setUser($user);
				}
				$beneficiary = $beneficiaryRepository->createBeneficiary($data['beneficiaryRightsOwner'] ?? [], $userId);
				if ($beneficiary) {
					$beneficiary->setUser($user);
				}
				$politicalPosition = $politicalPositionRepository->createPoliticalPosition($data['politicalPositionDetails'] ?? [], $userId);
				if ($politicalPosition) {
					$politicalPosition->setUser($user);
				}
				$financialDetails = $financialRepository->createFinancialDetails($data['financialDetails'] ?? [], $userId);

				if ($financialDetails) {
					$financialDetails->setUser($user);
				}
			}
			$this->entityManager->persist($user);
			if ($user->getMothersName()) {
				$this->entityManager->persist($address);
				$this->entityManager->persist($workDetails);
				$this->entityManager->persist($beneficiary);
				$this->entityManager->persist($politicalPosition);
				$this->entityManager->persist($financialDetails);
			}
			$this->entityManager->flush();
			$reference = $user->getId();
			$dateEmail = new DateTime();
			$dateEmailFormatted = $dateEmail->format('Y-m-d H:i:s');

			$pdfContent = $this->helper->generateReportPdf($data, $dateEmailFormatted, $reference);
			$pdfFileName = sprintf('%s_%s.pdf', $modifiedName, $mobileNumb);

			$ModifiedNameDB = '';
			for ($i = 0; $i < strlen($fullNameDB); $i++) {
				$char = $fullNameDB[$i];

				if (ctype_alpha($char)) {
					$ModifiedNameDB .= $char;
				} else {
					$i++;
				}
			}
			$folderName = $modifiedName . '-' . $mobileNumb;
			$folderNameDB = $ModifiedNameDB . '-' . $mobileNumbDB;

			$editdata = [
				'userId' => $userId,
				'oldFolderName' => $folderNameDB,
				'modifiedFolderName' => $folderName,
			];

			$pdfFile = tempnam(sys_get_temp_dir(), 'pdf');
			file_put_contents($pdfFile, $pdfContent);

			$client = new \GuzzleHttp\Client();
			$payload = [
				'multipart' => [
					[
						'name' => 'data',
						'contents' => json_encode($editdata),
						'headers' => ['Content-Type' => 'application/json'],
					],
					[
						'name' => 'pdf',
						'contents' => fopen($pdfFile, 'r'),
						'filename' => $pdfFileName,
						'headers' => ['Content-Type' => 'application/pdf'],
					],
				],
			];
			if ($user->getMothersName()) {
				$editdataImagesOnly =  [
					"frontImageID" => $frontImageID ? $frontImageID : null,
					"BackimageID" => $backImageID ? $backImageID : null,
					"imageRealEState" => $realStateImage ? $realStateImage : null,
					"imageotherdoc" => $otherDocumentImage ? $otherDocumentImage : null,
					"imageFrontaccoountStat" => $accountStatementImage ? $accountStatementImage : null,
					"imageEmployerLetter" => $employeeLetterImage ? $employeeLetterImage : null,
				];

				foreach ($editdataImagesOnly as $property => $image) {
					if ($image instanceof UploadedFile) {
						$extension = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
						$payload['multipart'][] = [
							'name' => 'images[' . $property . ']',
							'contents' => fopen($image->getPathname(), 'r'),
							'filename' => $property . '.' . $extension,
							'headers' => ['Content-Type' => $image->getMimeType()],
						];
					}
				}
			}

			$response = $client->request('POST', 'http://nbk.lss/edituserapi', $payload);

			$responseData = json_decode($response->getBody()->getContents(), true);

				$branchEmails = [
					1 => "sanayehbr@nbk.com.lb",
					2 => "Bhamdounbr@nbk.com.lb",
					3 => "PrivateBanking@nbk.com.lb",
				];
				if (isset($branchEmails[$branchId])) {
					$emailToSet = $branchEmails[$branchId];
					$queryBuilder = $this->entityManager->createQueryBuilder();
					$queryBuilder->select('e')
						->from('App\Entity\Emails', 'e')
						->where('e.user_id = :reference')
						->andWhere('e.identifier = :identifier')
						->setParameter('reference', $reference)
						->setParameter('identifier', 'branch');
					$emailRecord = $queryBuilder->getQuery()->getOneOrNullResult();
					if ($emailRecord) {
						$emailRecord->setReceiver($emailToSet);
						$this->entityManager->persist($emailRecord);
						$this->entityManager->flush();
					}
				}
	
			return $this->redirectToRoute('user_info', ['id' => $userId]);
		}
		return $this->render('nbkusers/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user[0],
			'motherNameExists' => $user[0]->getMothersName(),

		]);
	}

	#[Route('/print-pdf/{id}', name: 'print_pdf', methods: ['GET'])]
	public function printpdf($id)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'http://nbk.lss/print-pdf/' . $id);

		if ($response->getStatusCode() === 200) {


			$data = $this->entityManager->getRepository(Users::class)->find($id);
			$mobileNumb = $data->getMobileNumb();
			$fullName =  $data->getFullName();
			$modifiedName = '';
			for ($i = 0; $i < strlen($fullName); $i++) {
				$char = $fullName[$i];
				if (ctype_alpha($char)) {
					$modifiedName .= $char;
				} else {
					$i++;
				}
			}
			$pdfname = $modifiedName . '_' . $mobileNumb . '.pdf';
			$folderName = $modifiedName . '-' . $mobileNumb;


			return new Response(
				$response->getBody()->getContents(),
				200,
				[
					'Content-Type' => 'application/pdf',
					'Content-Disposition' => "attachment; filename= $pdfname",
				]
			);
		}
		$this->addFlash('error', 'Failed to download the PDF file.');
		return $this->redirectToRoute('user_info', ['id' => $id]);
	}
}
