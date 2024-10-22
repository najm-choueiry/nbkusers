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

class DefaultController extends AbstractController
{
	private $entityManager;
	private $loggerInterface;




	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $loggerInterface)
	{
		$this->entityManager = $entityManager;
		$this->loggerInterface = $loggerInterface;
	}

  #[Route('/', name: 'app_nbk_users')]
    public function index( Request $request, PaginatorInterface $paginator): Response
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

		$branchEmails = [1 => "sanayehbr@nbk.com.lb", 2 => "Bhamdounbr@nbk.com.lb", 3 => "PrivateBanking@nbk.com.lb"];
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
			->addSelect('a') // Ensure the associated collections are selected
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
				'address_class'=>Address::class,
				'WorkDetails_class'=>WorkDetails::class,
				'broDetails_class'=>BeneficiaryRightsOwner::class,
				'PoliticalPosition_class'=>PoliticalPositionDetails::class,
				'user_id' => $id,
			]);
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid())
			{
				$formData = $form->getData();
				return $this->forward('App\Controller\DefaultController::submit', [
					'id' => $id,
					'formData' => $formData, 
					'usersRepository' => $usersRepository,
					'addressRepository' => $addressRepository,
					'workDetailsRepository' => $workDetailsRepository,
					'beneficiaryRepository' => $beneficiaryRepository,
					'politicalPositionRepository' => $politicalPositionRepository,
					'financialRepository' => $financialRepository,
				]);
			}
		return $this->render('nbkusers/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user,
		]);
	}


	#[Route('/submit-data/{id}', name: 'submitData', methods: ['POST'], requirements: ['id' => '\d*'])]
	public function submit(Request $request, UsersRepository $usersRepository, AddressRepository $addressRepository, WorkDetailsRepository $workDetailsRepository, BeneficiaryRightsOwnerRepository $beneficiaryRepository, PoliticalPositionDetailsRepository $politicalPositionRepository, FinancialDetailsRepository $financialRepository): JsonResponse
	{
		$userId = $request->attributes->get('id');

			$data = json_decode($request->getContent(), true);
		if ($data === null) {
			return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
		}
	
		$branchEmail = $this->getBranchEmail($data['user']['branchId']);
		// Create and save User entity using UserRepository
		$user = $usersRepository->createUser($data['user']);

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

		$imageParts = explode(';base64,', $frontImageID);
		$imageBase64 = $imageParts[1];
		$imageType = explode('/', $imageParts[0])[1];
		//back
		$imagePartsBack = explode(';base64,', $backImageID);
		$imageBase64Back = $imagePartsBack[1];
		$imageTypeBack = explode('/', $imagePartsBack[0])[1];
		// Prepare the file path
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

		$mobileNumb = $data['user']['mobileNumb'];
		$folderName = $modifiedName . '-' . $mobileNumb;
		$imageFolder = 'imageUser/' . str_replace(' ', '_', $folderName);
		$publicDir = $this->getParameter('kernel.project_dir') . '/public/' . $imageFolder;

		$imageRealEState = '';

		if (!empty($realStateImage)) {
			$imagePartsrealestate = explode(';base64,', $realStateImage);
			$imageBase64realestate = $imagePartsrealestate[1];
			$imageTyperealestate = explode('/', $imagePartsrealestate[0])[1];
			$imageRealEState = 'imageUser/' . $folderName . '/imageRealEState.' . $imageTyperealestate;
			$imageContentRealState = base64_decode($imageBase64realestate);
		}

		$imageotherdoc = '';

		if (!empty($otherDocumentImage)) {
			//other doc
			$imagePartsotherDocument = explode(';base64,', $otherDocumentImage);
			$imageBase64otherDocument = $imagePartsotherDocument[1];
			$imageTypeotherDocument = explode('/', $imagePartsotherDocument[0])[1];
			$imageotherdoc = 'imageUser/' . $folderName . '/imageotherdoc.' . $imageTypeotherDocument;
			$imageContentOtherDoc = base64_decode($imageBase64otherDocument);
		}

		$imageFrontaccoountStat = '';

		if (!empty($accountStatementImage)) {
			//account statement
			$imagePartsaccountStatement = explode(';base64,', $accountStatementImage);
			$imageBase64accountstatement = $imagePartsaccountStatement[1];
			$imageTypeaccountstatement = explode('/', $imagePartsaccountStatement[0])[1];
			$imageFrontaccoountStat = 'imageUser/' . $folderName . '/imageFrontaccoountStat.' . $imageTypeaccountstatement;
			$imageContentFrontAccountStat = base64_decode($imageBase64accountstatement);
		}

		$imageEmployerLetter = '';
		if (!empty($employeeLetterImage)) {
			//account statement
			//employee
			$imagePartsemployee = explode(';base64,', $employeeLetterImage);
			$imageBase64employee = $imagePartsemployee[1];
			$imageTypeemployee = explode('/', $imagePartsemployee[0])[1];
			$imageEmployerLetter = 'imageUser/' . $folderName . '/imageEmployerLetter.' . $imageTypeemployee;
			$imageContentEmployementLetter = base64_decode($imageBase64employee);
		}

		if (!file_exists($publicDir)) {
			mkdir($publicDir, 0777, true);
		}
		$imagePath = $publicDir . '/frontImageID.' . $imageType;
		$imageFront = 'imageUser/' . $folderName . '/frontImageID.' . $imageTypeBack;
		$imageBack = 'imageUser/' . $folderName . '/BackimageID.' . $imageTypeBack;

		// Decode the base64 data and save the file
		$imageContent = base64_decode($imageBase64);
		$imageContentBack = base64_decode($imageBase64Back);

		if (file_put_contents($imagePath, $imageContent) === false || file_put_contents($imageBack, $imageContentBack) === false) {
			return new JsonResponse(['error' => 'Failed to save image content'], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

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
		$beneficiary = $beneficiaryRepository->createBeneficiary($data['beneficiaryRightsOwner'] ?? [],$userId);
		if ($beneficiary) {
			$beneficiary->setUser($user);
		}
		// Create and save PoliticalPositionDetails entity using PoliticalPositionDetailsRepository
		$politicalPosition = $politicalPositionRepository->createPoliticalPosition($data['politicalPositionDetails'] ?? [] ,$userId);
		if ($politicalPosition) {
			$politicalPosition->setUser($user);
		}
		// Create and save FinancialDetails entity using FinancialDetailsRepository
		$financialDetails = $financialRepository->createFinancialDetails($data['financialDetails'] ?? [], $imageFront, $imageBack, $imageRealEState, $imageFrontaccoountStat,  $imageotherdoc, $imageEmployerLetter,$userId);

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

		$files = scandir($folderdirectory);

		$images = array();
		$i = 0;

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}
			$filePath = $folderdirectory . '/' . $file;
			if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png', 'gif'))) {
				$images[$i] = $filePath;
				$i++;
			}
		}

		$reference = $user->getId();

		$dateEmail = new DateTime();
		$dateEmailFormatted = $dateEmail->format('Y-m-d H:i:s');
		$pdfContent = $this->generateReportPdf($data, $dateEmailFormatted, $reference);

		$pdfFileName = sprintf('%s_%s.pdf', $modifiedName, $data['user']['mobileNumb']);
		$pdfFilePath = $folderdirectory . '/' . $pdfFileName;
		// dd($pdfFileName, $pdfFilePath);
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

			// user email content
			$email = new Emails();
			$email->setUserId($reference);
			$email->setReceiver($data['user']['email']);
			$email->setSubject("Thank you for choosing NBK Lebanon.");
			$email->setContent($userEmailContent);
			$email->setIdentifier("New Relation");
			$email->setFilesPath("");
			$email->setStatus("Pending");
			$this->entityManager->persist($email);
			$this->entityManager->flush();

			// branch email content
			$email = new Emails();
			$email->setUserId($reference);
			$email->setReceiver($branchEmail);
			$email->setSubject('Form submitted from ' . $data['user']['fullName']);
			$email->setContent($branchEmailContent);
			$email->setIdentifier("Branch");
			$email->setFilesPath($folderdirectory);
			$email->setContents(json_encode($images));
			$email->setStatus("Pending");
			$this->entityManager->persist($email);
			$this->entityManager->flush();
			return new JsonResponse(['message' => 'Data saved successfully'], Response::HTTP_OK);
		}else {
			$email = $this->entityManager->getRepository(Emails::class)->findOne(['user_id'=>$userId]);
			$email->setContents(json_encode($images));
			$this->entityManager->persist($email);
			$this->entityManager->flush();
		}
		
		return $this->redirectToRoute('user_info', ['id' => $userId]);
	}
	

}
