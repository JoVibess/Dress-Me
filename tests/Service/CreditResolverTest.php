<?php

namespace App\Tests\Service;

use App\Entity\CreditBatch;
use App\Entity\Subscription;
use App\Entity\User;
use App\Enum\CreditSourceType;
use App\Service\CreditResolver;
use PHPUnit\Framework\TestCase;

final class CreditResolverTest extends TestCase
{
    public function testResolveAvailableCreditsCountsOnlyActiveSubscriptionsAndNonNegativeCredits(): void
    {
        $user = new User();

        $activeSubscription = (new Subscription())
            ->setStatus(Subscription::STATUS_ACTIVE)
            ->setStartsAt(new \DateTimeImmutable('2026-01-01'));
        $activeSubscription->addCreditBatch(
            (new CreditBatch())
                ->setType(CreditSourceType::SUBSCRIPTION)
                ->setInitialAmount(10)
                ->setRemainingAmount(7),
        );
        $activeSubscription->addCreditBatch(
            (new CreditBatch())
                ->setType(CreditSourceType::SUBSCRIPTION)
                ->setInitialAmount(5)
                ->setRemainingAmount(-2),
        );

        $cancelledSubscription = (new Subscription())
            ->setStatus(Subscription::STATUS_CANCELLED)
            ->setStartsAt(new \DateTimeImmutable('2026-01-01'));
        $cancelledSubscription->addCreditBatch(
            (new CreditBatch())
                ->setType(CreditSourceType::SUBSCRIPTION)
                ->setInitialAmount(12)
                ->setRemainingAmount(12),
        );

        $user->addSubscription($activeSubscription);
        $user->addSubscription($cancelledSubscription);

        $resolver = new CreditResolver();

        self::assertSame(7, $resolver->resolveAvailableCredits($user));
    }
}
