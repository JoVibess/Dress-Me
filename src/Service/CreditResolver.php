<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\User;

final class CreditResolver
{
    public function resolveAvailableCredits(User $user): int
    {
        $totalCredits = 0;

        foreach ($user->getSubscriptions() as $subscription) {
            if (Subscription::STATUS_ACTIVE !== $subscription->getStatus()) {
                continue;
            }

            foreach ($subscription->getCreditBatches() as $creditBatch) {
                $totalCredits += max(0, $creditBatch->getRemainingAmount() ?? 0);
            }
        }

        return $totalCredits;
    }
}
