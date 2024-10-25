<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\BeneficiaryRightsOwner;
use App\Entity\FinancialDetails;
use App\Entity\PoliticalPositionDetails;
use App\Entity\Users;
use App\Entity\WorkDetails;
use App\Entity\Logs;
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
use App\Service\GenerallServices;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class DefaultController extends AbstractController
{
	private $entityManager;
	private $loggerInterface;
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $loggerInterface)
	{
		$this->entityManager = $entityManager;
		$this->loggerInterface = $loggerInterface;
	}
	public function generateReportPdf(array $data, $time = null, $userreference = null): string
	{
		if (!$time) {
			$time = new DateTime();
		}
		// Define the utf8EncodeArray function correctly
		$utf8EncodeArray = function ($input) use (&$utf8EncodeArray) {
			if (is_array($input)) {
				return array_map($utf8EncodeArray, $input); // Recursively apply utf8_encode
			}
			if ($input instanceof DateTime) {
				return $input->format('Y-m-d H:i:s'); // Format DateTime as string
			}
			return $input !== null ? utf8_encode($input) : null;
		};
		// Encode the provided data
		$userreference = $utf8EncodeArray($userreference);
		$user = $utf8EncodeArray($data['user']);
		$address = $utf8EncodeArray($data['address']);
		$workDetails = $utf8EncodeArray($data['workDetails']);
		$beneficiaryRightsOwner = $utf8EncodeArray($data['beneficiaryRightsOwner']);
		$politicalPositionDetails = $utf8EncodeArray($data['politicalPositionDetails']);
		$financialDetails = $utf8EncodeArray($data['financialDetails']);
		// Generate the HTML for the PDF
		$html = $this->renderView('pdf/report.html.twig', [
			'reference' => $userreference,
			'user' => $user,
			'address' => $address,
			'workDetails' => $workDetails,
			'beneficiaryRightsOwner' => $beneficiaryRightsOwner,
			'politicalPositionDetails' => $politicalPositionDetails,
			'financialDetails' => $financialDetails,
			'time' => $time
		]);
		// Set up and render PDF
		$dompdf = new Dompdf();
		$dompdf->set_option('defaultFont', 'Helvetica');
		$dompdf->set_option('isHtml5ParserEnabled', true);
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->loadHtml($html);
		$dompdf->render();
		return $dompdf->output();
	}
	#[Route('/', name: 'app_nbk_users')]
	public function index(Request $request, PaginatorInterface $paginator): Response
	{
		// Handle the list of users when no $id is provided
		$sortField = $request->query->get('sort', 'u.id');
		$sortDirection = $request->query->get('direction', 'DESC');

		// Fetch all users from the database
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
		// Fetch all users from the database
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
	public function getBranchEmail($branchId)
	{

		// $branchEmails = [1 => "sanayehbr@nbk.com.lb", 2 => "Bhamdounbr@nbk.com.lb", 3 => "PrivateBanking@nbk.com.lb"];
		$branchEmails = [1 => "eliaschaaya97@gmail.com", 2 => "eliaschaaya97@gmail.com", 3 => "eliaschaaya97@gmail.com"];

		//$branchEmails = [1 => "zeina.abdallah@nbk.com.lb ", 2 => "maysaa.nasereddine@nbk.com.lb", 3 => "zeina.abdallah@nbk.com.lb "];
		if (array_key_exists($branchId, $branchEmails)) {
			return $branchEmails[$branchId];
		} else {
			return null;
		}
	}
	public function isValidEmail(string $email): bool
	{
		$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
		return preg_match($pattern, $email) === 1;
	}
	#[Route('/nbk/editInfo/{id}', name: 'edit_info')]
	public function editInfo($id, UsersRepository $usersRepository, AddressRepository $addressRepository, WorkDetailsRepository $workDetailsRepository, BeneficiaryRightsOwnerRepository $beneficiaryRepository, PoliticalPositionDetailsRepository $politicalPositionRepository, FinancialDetailsRepository $financialRepository, Request $request): Response
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
			->setParameter('id', $id)
			->getQuery()
			->getResult();
		$form = $this->createForm(EditType::class, null, [
			'user_class' => Users::class,
			'address_class' => Address::class,
			'WorkDetails_class' => WorkDetails::class,
			'broDetails_class' => BeneficiaryRightsOwner::class,
			'PoliticalPosition_class' => PoliticalPositionDetails::class,
			'FinancialDetails_class' => FinancialDetails::class,
			'user_id' => $id,
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$formData = $form->getData();
			$jsonData = [
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
			$userId = $id;
			$data = $jsonData;
			if ($data === null) {
				return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
			}
			$branchId =$data['user']['branchId'];
			$dataDB = $this->entityManager->getRepository(Users::class)->find($id);
			$mobileNumbDB = $dataDB->getMobileNumb();
			$fullNameDB =  $dataDB->getFullName();
			// Create and save User entity using UserRepository
			$user = $usersRepository->createUser($data['user'], $userId,$branchId);
			if (!$user) {
				return new JsonResponse(['error' => 'Failed to create user'], Response::HTTP_BAD_REQUEST);
			}
			unset($data['user']['branchId']);
			$userEmail = $data['user']['email'];
			if (!$this->isValidEmail($userEmail)) {
				return new JsonResponse(['error' => 'Invalid email address'], Response::HTTP_BAD_REQUEST);
			}
			$frontImageID = $data['financialDetails']['frontImageID'];
			$backImageID = $data['financialDetails']['backImageID'];
			$realStateImage = $data['financialDetails']['realEstateTitle'];
			$otherDocumentImage = $data['financialDetails']['otherDocument'];
			$accountStatementImage = $data['financialDetails']['accountStatement'];
			$employeeLetterImage = $data['financialDetails']['employerLetter'];
			if (!empty($frontImageID)) {
				$imageType = explode('/', $frontImageID->getClientMimeType())[1];
				// dd($imageType );
				// $imageParts = explode(';base64,', $frontImageID);
				// $imageBase64 = $imageParts[1];
				// dd(	file_get_contents($frontImageID->getPathname()));
			}
			if (!empty($backImageID)) {
				//back
				$imageTypeBack = explode('/', $backImageID->getClientMimeType())[1];
				// $imagePartsBack = explode(';base64,', $backImageID);
				// $imageBase64Back = $imagePartsBack[1];
				// $imageTypeBack = explode('/', $imagePartsBack[0])[1];
				// Prepare the file path
			}
			$fullName = $data['user']['fullName'];
			// Initialize an empty string to store the modified name
			$modifiedName = '';
			for ($i = 0; $i < strlen($fullName); $i++) {
				$char = $fullName[$i];

				if (ctype_alpha($char)) {
					$modifiedName .= $char;
				} else {
					$i++;
				}
			}
			//    $secondProjectFolderPath = 'C:/xampp/htdocs/AlWatany-NBK/public/imageUser/test-9611231212';
			//    $filesystem = new Filesystem();
			//    if ($filesystem->exists($secondProjectFolderPath)) {
			// 	try {
			// 		// Remove the folder
			// 		$filesystem->remove($secondProjectFolderPath);
			// 		echo "Folder deleted successfully from the second project.";
			// 	} catch (IOExceptionInterface $exception) {
			// 		echo "An error occurred while deleting the folder at " . $secondProjectFolderPath;
			// 	}
			// } else {
			// 	echo "Folder does not exist.";
			// }
			// $mobileNumb = $data['user']['mobileNumb'];
			// $folderName = $modifiedName . '-' . $mobileNumb;
			// $imageFolder = 'imageUser/' . str_replace(' ', '_', $folderName);
			// $publicDir = $this->getParameter('kernel.project_dir') . '/public/' . $imageFolder;
			$staticBaseDir = 'C:/xampp/htdocs/AlWatany-NBK/public/';
			$ModifiedNameDB = '';
			for ($i = 0; $i < strlen($fullNameDB); $i++) {
				$char = $fullNameDB[$i];

				if (ctype_alpha($char)) {
					$ModifiedNameDB .= $char;
				} else {
					$i++;
				}
			}
			$oldfolderName = $ModifiedNameDB . '-' . $mobileNumbDB;
			$oldImageFolder = 'imageUser/' . str_replace(' ', '_', $oldfolderName);
			$oldFolderPath = $staticBaseDir . $oldImageFolder;
			$mobileNumb = $data['user']['mobileNumb'];
			$folderName = $modifiedName . '-' . $mobileNumb;
			$ImageFolder = 'imageUser/' . str_replace(' ', '_', $folderName);
			$FolderPath = $staticBaseDir . $ImageFolder;
			$filesystem = new Filesystem();
			if ($filesystem->exists($oldFolderPath)) {
				if ($FolderPath !== $oldFolderPath) {

					$filesystem->rename($oldFolderPath, $FolderPath);
					if ($filesystem->exists($oldFolderPath)) {
						$filesystem->remove($oldFolderPath);
					}
				}
			}
			$imageRealEState = '';
			$imageRealEStateDB = '';
			if (!empty($realStateImage)) {
				// $imagePartsrealestate = explode(';base64,', $realStateImage);
				// $imageBase64realestate = $imagePartsrealestate[1];
				// $imageTyperealestate = explode('/', $imagePartsrealestate[0])[1];
				$baseImagePath = $staticBaseDir . 'imageUser/' . $folderName . '/imageRealEState';

				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}
				$imageTyperealestate = explode('/', $realStateImage->getClientMimeType())[1];

				$imageRealEState = $staticBaseDir . 'imageUser/' . $folderName . '/imageRealEState.' . $imageTyperealestate;
				$imageRealEStateDB =  'imageUser/' . $folderName . '/imageRealEState.' . $imageTyperealestate;
				// $imageContentRealState = base64_decode($imageBase64realestate);
				$imageContentRealState = file_get_contents($realStateImage->getPathname());
			}
			$imageotherdoc = '';
			$imageotherdocDB = '';
			if (!empty($otherDocumentImage)) {
				//other doc
				// $imagePartsotherDocument = explode(';base64,', $otherDocumentImage);
				// $imageBase64otherDocument = $imagePartsotherDocument[1];
				// $imageTypeotherDocument = explode('/', $imagePartsotherDocument[0])[1];
				$baseImagePath = $staticBaseDir . 'imageUser/' . $folderName . '/imageotherdoc';
				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}
				$imageTypeotherDocument = explode('/', $otherDocumentImage->getClientMimeType())[1];
				$imageotherdoc = $staticBaseDir . 'imageUser/' . $folderName . '/imageotherdoc.' . $imageTypeotherDocument;
				$imageotherdocDB =  'imageUser/' . $folderName . '/imageotherdoc.' . $imageTypeotherDocument;
				// $imageContentOtherDoc = base64_decode($imageBase64otherDocument);
				$imageContentOtherDoc = file_get_contents($otherDocumentImage->getPathname());
			}
			$imageFrontaccoountStat = '';
			$imageFrontaccoountStatDB = '';
			if (!empty($accountStatementImage)) {
				// $imagePartsaccountStatement = explode(';base64,', $accountStatementImage);
				// $imageBase64accountstatement = $imagePartsaccountStatement[1];
				// $imageTypeaccountstatement = explode('/', $imagePartsaccountStatement[0])[1];
				$baseImagePath = $staticBaseDir . 'imageUser/' . $folderName . '/imageFrontaccoountStat';
				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}
				$imageTypeaccountstatement = explode('/', $accountStatementImage->getClientMimeType())[1];
				$imageFrontaccoountStat = $staticBaseDir . 'imageUser/' . $folderName . '/imageFrontaccoountStat.' . $imageTypeaccountstatement;
				$imageFrontaccoountStatDB =  'imageUser/' . $folderName . '/imageFrontaccoountStat.' . $imageTypeaccountstatement;
				// $imageContentFrontAccountStat = base64_decode($imageBase64accountstatement);
				$imageContentFrontAccountStat = file_get_contents($accountStatementImage->getPathname());
			}
			$imageEmployerLetter = '';
			$imageEmployerLetterDB = '';
			if (!empty($employeeLetterImage)) {
				// $imagePartsemployee = explode(';base64,', $employeeLetterImage);
				// $imageBase64employee = $imagePartsemployee[1];
				// $imageTypeemployee = explode('/', $imagePartsemployee[0])[1];
				$baseImagePath = $staticBaseDir . 'imageUser/' . $folderName . '/imageEmployerLetter';
				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}
				$imageTypeemployee = explode('/', $employeeLetterImage->getClientMimeType())[1];
				$imageEmployerLetter = $staticBaseDir . 'imageUser/' . $folderName . '/imageEmployerLetter.' . $imageTypeemployee;
				$imageEmployerLetterDB = 'imageUser/' . $folderName . '/imageEmployerLetter.' . $imageTypeemployee;

				// $imageContentEmployementLetter = base64_decode($imageBase64employee);
				$imageContentEmployementLetter = file_get_contents($employeeLetterImage->getPathname());
			}

			if (!file_exists($FolderPath)) {
				mkdir($FolderPath, 0777, true);
			}
			$imageFront = null;
			$imageFrontDB = null;
			$imageBack = null;
			$imageBackDB = null;
			if (isset($frontImageID)) {
				// $imagePath = $publicDir . '/frontImageID.' . $imageType;
				$baseImagePath = $staticBaseDir . 'imageUser/' . $folderName . '/frontImageID';
				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}

				$imageFront = $staticBaseDir . 'imageUser/' . $folderName . '/frontImageID.' . $imageType;
				$imageFrontDB = 'imageUser/' . $folderName . '/frontImageID.' . $imageType;

				$imageContent = file_get_contents($frontImageID->getPathname());
				if (file_put_contents($imageFront, $imageContent) === false) {
					return new JsonResponse(['error' => 'Failed to save image content'], Response::HTTP_INTERNAL_SERVER_ERROR);
				}
			}
			if (isset($backImageID)) {
				$baseImagePath =  $staticBaseDir . 'imageUser/' . $folderName . '/BackimageID';
				$extensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'webp', 'WEBP'];
				foreach ($extensions as $ext) {
					$oldImagePath = $baseImagePath . '.' . $ext;
					if (file_exists($oldImagePath)) {
						unlink($oldImagePath);
						break;
					}
				}

				$imageBack = $staticBaseDir . 'imageUser/' . $folderName . '/BackimageID.' . $imageTypeBack;
				$imageBackDB = 'imageUser/' . $folderName . '/BackimageID.' . $imageType;

				$imageContentBack = file_get_contents($backImageID->getPathname());
				if (file_put_contents($imageBack, $imageContentBack) === false) {
					return new JsonResponse(['error' => 'Failed to save image content'], Response::HTTP_INTERNAL_SERVER_ERROR);
				}
			}
			// Decode the base64 data and save the file
			// $imageContent = base64_decode($imageBase64);
			// $imageContent = file_get_contents($frontImageID->getPathname());
			// $imageContentBack = file_get_contents($backImageID->getPathname());
			// $imageContentBack = base64_decode($imageBase64Back);
			if (isset($realStateImage)) {
				file_put_contents($imageRealEState, $imageContentRealState);
			}
			if (isset($otherDocumentImage)) {
				file_put_contents($imageotherdoc, $imageContentOtherDoc);
			}
			if (isset($accountStatementImage)) {
				file_put_contents($imageFrontaccoountStat, $imageContentFrontAccountStat);
			}
			if (isset($employeeLetterImage)) {
				file_put_contents($imageEmployerLetter, $imageContentEmployementLetter);
			}
			unset($data['financialDetails']['frontImageID']);
			unset($data['financialDetails']['backImageID']);
			unset($data['financialDetails']['employerLetter']);
			unset($data['financialDetails']['otherDocument']);
			unset($data['financialDetails']['accountStatement']);
			unset($data['financialDetails']['realEstateTitle']);
			// Set the user for Address and WorkDetails
			$address = $addressRepository->createAddress($data['address'] ?? [], $userId);
			if ($address) {
				$address->setUser($user);
			}
			$workDetails = $workDetailsRepository->createWorkDetails($data['workDetails'] ?? [], $userId);
			if ($workDetails) {
				$workDetails->setUser($user);
			}
			// Create and save BeneficiaryRightsOwner entity using BeneficiaryRightsOwnerRepository
			$beneficiary = $beneficiaryRepository->createBeneficiary($data['beneficiaryRightsOwner'] ?? [], $userId);
			if ($beneficiary) {
				$beneficiary->setUser($user);
			}
			// Create and save PoliticalPositionDetails entity using PoliticalPositionDetailsRepository
			$politicalPosition = $politicalPositionRepository->createPoliticalPosition($data['politicalPositionDetails'] ?? [], $userId);
			if ($politicalPosition) {
				$politicalPosition->setUser($user);
			}
			// Create and save FinancialDetails entity using FinancialDetailsRepository
			$financialDetails = $financialRepository->createFinancialDetails($data['financialDetails'] ?? [], $userId, $imageFrontDB, $imageBackDB, $imageRealEStateDB, $imageFrontaccoountStatDB,  $imageotherdocDB, $imageEmployerLetterDB);

			if ($financialDetails) {
				$financialDetails->setUser($user);
			}
			// Persist and flush all entities
			$this->entityManager->persist($user);
			$this->entityManager->persist($address);
			$this->entityManager->persist($workDetails);
			$this->entityManager->persist($beneficiary);
			$this->entityManager->persist($politicalPosition);
			$this->entityManager->persist($financialDetails);
			$this->entityManager->flush();
			// $this->submitForm($user->getId());
			//GETTING THE IMAGES
			$publicDirectory = $_SERVER['DOCUMENT_ROOT'] . "/imageUser/";
			$folderdirectory = $publicDirectory . $folderName;
			$files = scandir($FolderPath);
			$images = array();
			$i = 0;
			foreach ($files as $file) {
				if ($file === '.' || $file === '..') {
					continue;
				}
				$filePath = $folderdirectory . '/' . $file;
				if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png', 'gif'))) {
					$images[$i] = $filePath;
					$i++;}}
			$reference = $user->getId();
			$dateEmail = new DateTime();
			$dateEmailFormatted = $dateEmail->format('Y-m-d H:i:s');
			$pdfContent = $this->generateReportPdf($data, $dateEmailFormatted, $reference);
			$pdfFileName = sprintf('%s_%s.pdf', $modifiedName, $data['user']['mobileNumb']);
			$pdfFileNameDB = sprintf('%s_%s.pdf', $ModifiedNameDB, $mobileNumbDB);
			$pdfFilePathDB = $FolderPath . '/' . $pdfFileNameDB;
			$pdfFilePath = $FolderPath . '/' . $pdfFileName;
			if (file_exists($pdfFilePathDB)) {
				unlink($pdfFilePathDB);
			}
			file_put_contents($pdfFilePath, $pdfContent);
			$images[$i] = $pdfFilePath;
			if ($userId !== null) {
				$branchEmailContent = '
					<p><strong>Application REF:</strong> User-' . htmlspecialchars($reference) . '</p>
					<p><strong>The customer:</strong> ' . htmlspecialchars($data['user']['fullName']) . '</p>
					<p><strong>Number:</strong> ' . htmlspecialchars($data['user']['mobileNumb']) . '</p>
					<p><strong>Email:</strong> ' . htmlspecialchars($data['user']['email']) . '</p>
					<p><strong>Accessed on:</strong> ' . htmlspecialchars($dateEmailFormatted) . ' the Mobile Banking Application to submit a 						new account opening application.</p>
					<p>Please contact the customer within 3-5 days since it is a new relation.</p>
					';
				$userEmailContent = '
					<p>Dear ' . htmlspecialchars($data['user']['fullName']) . '</p>
					</br>
					</br>
					<p>Thank you for choosing NBK Lebanon.\nWe will contact you within 3-5 days</p>
					</br>
					<p>Regards</p>
					';

                //branch receiver mail change

	             $reference = $user->getId();
				 $branchId;
				 $branchEmails = [
					1 => "sanayehbr@nbk.com.lb",
					2 => "Bhamdounbr@nbk.com.lb",
					3 => "PrivateBanking@nbk.com.lb",
				];
				if (isset($branchEmails[$branchId])) {
					$emailToSet = $branchEmails[$branchId];
					$queryBuilder = 	$this->entityManager->createQueryBuilder();
					$queryBuilder->select('e')
						->from('App\Entity\Emails', 'e')
						->where('e.user_id = :reference')
						->andWhere('e.identifier = :identifier')
						->setParameter('reference', $reference)
						->setParameter('identifier', 'branch');

					$emailRecord = $queryBuilder->getQuery()->getOneOrNullResult();
			
					if ($emailRecord) {
						$emailRecord->setReceiver($emailToSet);
						$this->entityManager->flush();
					} }

				// user email content
				// $email = new Emails();
				// $email->setUserId($reference);
				// $email->setReceiver($data['user']['email']);
				// $email->setSubject("Thank you for choosing NBK Lebanon.");
				// $email->setContent($userEmailContent);
				// $email->setIdentifier("New Relation");
				// $email->setFilesPath("");
				// $email->setStatus("Pending");
				// $this->entityManager->persist($email);
				// $this->entityManager->flush();

				// branch email content
				// $email = new Emails();
				// $email->setUserId($reference);
				// $email->setReceiver($branchEmail);
				// $email->setSubject('Form submitted from ' . $data['user']['fullName']);
				// $email->setContent($branchEmailContent);
				// $email->setIdentifier("Branch");
				// $email->setFilesPath($folderdirectory);
				// $email->setContents(json_encode($images));
				// $email->setStatus("Pending");
				// $this->entityManager->persist($email);
				// $this->entityManager->flush();
				return $this->redirectToRoute('user_info', ['id' => $userId]);
			} else {
				$email = $this->entityManager->getRepository(Emails::class)->findOne(['user_id' => $userId]);
				$email->setContents(json_encode($images));
				$this->entityManager->persist($email);
				$this->entityManager->flush();
			}
			return $this->redirectToRoute('user_info', ['id' => $userId]);
		}
		return $this->render('nbkusers/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user[0],
		]);
	}

	#[Route('/print-pdf/{id}', name: 'print_pdf', methods: ['GET'])]
	public function printpdf($id)
	{
		$data = $this->entityManager->getRepository(Users::class)->find($id);
		$mobileNumb = $data->getMobileNumb();
		$fullName =  $data->getFullName();
		$modifiedName = '';
		for ($i = 0; $i < strlen($fullName); $i++) {
			$char = $fullName[$i];
			if (ctype_alpha($char)) {
				$modifiedName .= $char;} 
			else {$i++;}}
		$pdfname = $modifiedName . '_' . $mobileNumb . '.pdf';
		$folderName = $modifiedName . '-' . $mobileNumb;
		$staticBaseDir = 'C:/xampp/htdocs/AlWatany-NBK/public/imageUser/' . $folderName . '/';
		$pdfFilePath = $staticBaseDir . $pdfname;
		if (!file_exists($pdfFilePath)) {
			// Set a flash message for file not found
			$this->addFlash('error', 'PDF file not found.');
			return $this->redirectToRoute('app_nbk_users');
		}
		$response = new Response(file_get_contents($pdfFilePath));
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'attachment; filename="' . basename($pdfFilePath) . '"');
		return $response;
	}
}
