<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\Accounts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {    
        dd($request);
        if ($request->isMethod('POST')) {
            $username = $request->request->get('_username');
            $password = $request->request->get('_password');
       
            $account = $this->entityManager->getRepository(Accounts::class)->findOneBy(['username' => $username]);
          
            if ($account && $password === $account->getPassword()) {
              
                return $this->redirectToRoute('app_nbk_users'); 
            } else {
                $this->addFlash('error', 'Invalid username or password.');
            }
        }
        return $this->render('security/login.html.twig');
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        return $this->redirectToRoute('app_login');
    }
}
