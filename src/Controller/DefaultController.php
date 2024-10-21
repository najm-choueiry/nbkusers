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
	#[Route('/{id}', name: 'app_nbk_users_edit', requirements: ['id' => '\d+'], defaults: ['id' => null])]
	public function index($id = null): Response
	{

		if ($id) {
			$form = $this->createForm(EditType::class, null, [
				'user_class' => Users::class,
				'user_id' => $id,
			]);

			return $this->render('nbkusers/edit.html.twig', [
				'form' => $form->createView(),
			]);
		}
		return $this->render('nbkusers/index.html.twig', [
			'test' => true,
		]);
	}
}
