<?php

namespace App\Controller\Middle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/app/dashboard', name: 'middle_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('middle/dashboard.html.twig');
    }
}
