<?php

namespace App\Controller\Middle;

use App\Entity\ApiToken;
use App\Entity\CreditBatch;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/app/dashboard', name: 'middle_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('front_home');
        }

        $totalCredits = 0;
        $apiTokens = [];
        $creditBatches = [];

        foreach ($user->getSubscriptions() as $subscription) {
            foreach ($subscription->getCreditBatches() as $creditBatch) {
                $totalCredits += $creditBatch->getRemainingAmount() ?? 0;
                $creditBatches[] = $creditBatch;
            }

            foreach ($subscription->getApiTokens() as $apiToken) {
                $apiTokens[] = $apiToken;
            }
        }

        usort(
            $creditBatches,
            static fn (CreditBatch $a, CreditBatch $b): int => ($b->getCreatedAt()?->getTimestamp() ?? 0) <=> ($a->getCreatedAt()?->getTimestamp() ?? 0),
        );

        usort(
            $apiTokens,
            static fn (ApiToken $a, ApiToken $b): int => ($b->getCreatedAt()?->getTimestamp() ?? 0) <=> ($a->getCreatedAt()?->getTimestamp() ?? 0),
        );

        return $this->render('middle/dashboard.html.twig', [
            'current_user' => $user,
            'total_credits' => $totalCredits,
            'api_tokens' => $apiTokens,
            'latest_credit_batches' => array_slice($creditBatches, 0, 5),
        ]);
    }
}
