<?php

namespace App\Controller\Front;

use App\Enum\CreditSourceType;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OfferController extends AbstractController
{
    #[Route('/offers', name: 'front_offers', methods: ['GET'])]
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('front/offers.html.twig', [
            'subscription_offers' => $offerRepository->findBy(
                ['type' => CreditSourceType::SUBSCRIPTION],
                ['price' => 'ASC'],
            ),
            'one_time_offers' => $offerRepository->findBy(
                ['type' => CreditSourceType::ONE_TIME_PURCHASE],
                ['price' => 'ASC'],
            ),
        ]);
    }
}
