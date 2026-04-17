<?php

namespace App\Controller\Middle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/app/payment', name: 'middle_payment', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('middle/payment.html.twig');
    }

    #[Route('/app/payment-confirmation', name: 'middle_payment_confirmation', methods: ['GET'])]
    public function confirmation(): Response
    {
        return $this->render('middle/payment_confirmation.html.twig');
    }
}
