<?php

namespace App\Controller\Middle;

use App\Entity\CreditBatch;
use App\Entity\Subscription;
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

        $activeSubscription = null;

        foreach ($user->getSubscriptions() as $subscription) {
            if (Subscription::STATUS_ACTIVE === $subscription->getStatus()) {
                $activeSubscription = $subscription;
                break;
            }
        }

        $activeToken = null;
        $creditBatches = [];
        $totalCredits = 0;

        foreach ($user->getApiTokens() as $apiToken) {
            if ($apiToken->isActive()) {
                $activeToken = $apiToken;
                break;
            }
        }

        if (null !== $activeSubscription) {
            foreach ($activeSubscription->getCreditBatches() as $creditBatch) {
                $totalCredits += $creditBatch->getRemainingAmount() ?? 0;
                $creditBatches[] = $creditBatch;
            }
        }

        usort(
            $creditBatches,
            static fn (CreditBatch $a, CreditBatch $b): int => ($b->getCreatedAt()?->getTimestamp() ?? 0) <=> ($a->getCreatedAt()?->getTimestamp() ?? 0),
        );

        return $this->render('middle/dashboard.html.twig', [
            'current_user' => $user,
            'active_subscription' => $activeSubscription,
            'active_token' => $activeToken,
            'total_credits' => $totalCredits,
            'credit_usage_chart' => $this->buildCreditUsageChartData($creditBatches),
            'latest_credit_batches' => array_slice($creditBatches, 0, 5),
        ]);
    }

    /**
     * @param CreditBatch[] $creditBatches
     *
     * @return array{labels: string[], series: array<int, array{name: string, data: int[]}>}
     */
    private function buildCreditUsageChartData(array $creditBatches): array
    {
        $recentBatches = array_slice($creditBatches, 0, 12);
        $recentBatches = array_reverse($recentBatches);

        $labels = [];
        $usageRates = [];

        foreach ($recentBatches as $creditBatch) {
            $initialAmount = $creditBatch->getInitialAmount() ?? 0;
            $remainingAmount = $creditBatch->getRemainingAmount() ?? 0;
            $usedAmount = max(0, $initialAmount - $remainingAmount);
            $usageRate = $initialAmount > 0 ? (int) round($usedAmount / $initialAmount * 100) : 0;

            $labels[] = $creditBatch->getCreatedAt()?->format('Y-m-d') ?? 'Unknown';
            $usageRates[] = $usageRate;
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Usage rate',
                    'data' => $usageRates,
                ],
            ],
        ];
    }
}
