<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'front_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/features', name: 'front_features', methods: ['GET'])]
    public function features(): Response
    {
        return $this->render('front/features.html.twig');
    }

    #[Route('/contact', name: 'front_contact', methods: ['GET', 'POST'])]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig');
    }
}
