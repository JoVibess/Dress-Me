<?php

namespace App\Controller\Middle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/app/account', name: 'middle_account', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('middle/account.html.twig');
    }

    #[Route('/app/account/payments', name: 'middle_account_payments', methods: ['GET'])]
    public function payments(): Response
    {
        return $this->render('middle/account.html.twig');
    }

    #[Route('/app/account/credits', name: 'middle_account_credits', methods: ['GET'])]
    public function credits(): Response
    {
        return $this->render('middle/account.html.twig');
    }
}
