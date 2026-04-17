<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OfferController extends AbstractController
{
    #[Route('/admin/offers', name: 'admin_offers', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/offers.html.twig');
    }

    #[Route('/admin/offers/{id}/edit', name: 'admin_offer_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id): Response
    {
        return $this->render('admin/offer_edit.html.twig', [
            'offer_id' => $id,
        ]);
    }
}
