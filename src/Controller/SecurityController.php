<?php

namespace App\Controller;

use App\Entity\Accounts;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
	private $entityManager;
	private $requestStack;

	public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
	{
		$this->entityManager = $entityManager;
		$this->requestStack = $requestStack;
	}

	#[Route('/login', name: 'app_login')]
	public function login(Request $request): Response
	{
		$response = $this->render('security/login.html.twig');
		$response->headers->set('Cache-Control', 'no-store');
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('Expires', '0');

		if ($request->isMethod('POST')) {
			$username = $request->request->get('_username');
			$password = $request->request->get('_password');

			$account = $this->entityManager->getRepository(Accounts::class)->findOneBy(['username' => $username]);

			if ($account && password_verify($password, $account->getPassword())) {
				$this->requestStack->getCurrentRequest()->getSession()->set('user', $account->getId());
				return $this->redirectToRoute('app_nbk_users');
			} else {
				$this->addFlash('error', 'Invalid username or password.');
			}
		}
		return $response;
	}



	#[Route('/createNewUser', name: 'app_register')]
	public function register(Request $request): Response
	{
		if (!($this->requestStack->getCurrentRequest()->getSession()->get('user'))) {
			return $this->redirectToRoute('app_login');
		}
		$error = false;

		if ($request->isMethod('POST')) {
			$username = $request->request->get('_username');
			$password = $request->request->get('_password');

			$hashPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

			$existing = $this->entityManager->getRepository(Accounts::class)->findOneBy(['username' => $username]);

			if ($existing) {
				$error = 'User already exists';
			} else {
				$profile = new Accounts();
				$profile->setUsername($username);
				$profile->setPassword($hashPassword);
				$this->entityManager->persist($profile);
				$this->entityManager->flush();

				$this->requestStack->getCurrentRequest()->getSession()->set('user', $profile->getId());
				return $this->redirectToRoute('app_nbk_users');
			}
		}
		return $this->render('security/register.html.twig', [
			'error' => $error,
		]);
	}



	#[Route('/logout', name: 'app_logout')]
	public function logout()
	{
		$this->requestStack->getCurrentRequest()->getSession()->remove('user');

		return $this->redirectToRoute('app_login');
	}
}
