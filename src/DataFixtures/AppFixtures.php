<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\CreditBatch;
use App\Entity\Offer;
use App\Entity\Subscription;
use App\Entity\User;
use App\Enum\CreditSourceType;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $createdUsers = $this->loadUsers($manager);
        $createdOffers = $this->loadOffers($manager);
        $this->loadSubscriptions($manager, $createdUsers, $createdOffers);

        $manager->flush();
    }

    /**
     * @return array<string, User>
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $users = [
            [
                'name' => 'Admin DressMe',
                'email' => 'admin@dressme.test',
                'website' => 'https://admin.dressme.test',
                'roles' => [UserRole::ADMIN],
                'plainPassword' => 'admin123',
            ],
        ];

        $firstNames = [
            'Alice', 'Ben', 'Chloe', 'David', 'Emma', 'Finn', 'Grace', 'Hugo', 'Iris', 'Jules',
            'Kara', 'Leo', 'Maya', 'Noah', 'Olivia', 'Paul', 'Quinn', 'Romy', 'Sarah', 'Theo',
            'Uma', 'Victor', 'Wendy', 'Xavier', 'Yara', 'Zack', 'Lina', 'Marco', 'Nina', 'Oscar',
            'Pia', 'Robin', 'Sofia', 'Tom', 'Vera', 'Will', 'Zoey', 'Eli', 'Mila', 'Adam',
            'Nora', 'Liam', 'Eva', 'Max', 'Clara', 'Nathan', 'Lola', 'Simon', 'Ava', 'Lucas',
        ];
        $lastNames = [
            'Martin', 'Carter', 'Nguyen', 'Lopez', 'Dubois', 'Smith', 'Bernard', 'Wilson', 'Garcia', 'Roux',
            'Petit', 'Taylor', 'Moreau', 'Brown', 'Laurent', 'Anderson', 'Leroy', 'Thomas', 'Girard', 'Moore',
            'Lambert', 'Jackson', 'Fontaine', 'White', 'Mercier', 'Harris', 'Dupont', 'Clark', 'Faure', 'Lewis',
            'Andre', 'Young', 'Morel', 'King', 'Fournier', 'Wright', 'Garnier', 'Scott', 'Chevalier', 'Green',
            'Francois', 'Baker', 'Perrin', 'Adams', 'Robin', 'Nelson', 'Clement', 'Hill', 'Henry', 'Campbell',
        ];
        $storeTypes = ['fashion', 'boutique', 'atelier', 'studio', 'wear', 'shop', 'store', 'market', 'catalog', 'outlet'];

        for ($index = 1; $index <= 50; ++$index) {
            $firstName = $firstNames[$index - 1];
            $lastName = $lastNames[$index - 1];
            $slug = strtolower($firstName.'-'.$lastName);
            $storeType = $storeTypes[($index - 1) % count($storeTypes)];

            $users[] = [
                'name' => $firstName.' '.$lastName,
                'email' => sprintf('merchant%02d@example.test', $index),
                'website' => sprintf('https://%s-%s.test', $slug, $storeType),
                'roles' => [UserRole::USER],
                'plainPassword' => 'user12345',
            ];
        }

        $createdUsers = [];

        foreach ($users as $userData) {
            $user = (new User())
                ->setName($userData['name'])
                ->setEmail($userData['email'])
                ->setWebsite($userData['website'])
                ->setRoles($userData['roles']);

            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['plainPassword']));

            $manager->persist($user);
            $createdUsers[$userData['email']] = $user;

            if (!\in_array(UserRole::ADMIN, $userData['roles'], true)) {
                $this->createApiToken($manager, $user, count($createdUsers) - 1);
            }
        }

        return $createdUsers;
    }

    /**
     * @return array<string, Offer>
     */
    private function loadOffers(ObjectManager $manager): array
    {
        $offers = [
            [
                'name' => 'Starter Monthly',
                'description' => 'Monthly subscription for small WooCommerce stores starting virtual try-on.',
                'price' => '19.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 100,
                'durationInMonths' => 1,
            ],
            [
                'name' => 'Growth Monthly',
                'description' => 'Monthly subscription for growing stores with regular virtual try-on usage.',
                'price' => '49.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 350,
                'durationInMonths' => 1,
            ],
            [
                'name' => 'Scale Monthly',
                'description' => 'Monthly subscription for high-volume stores and advanced catalog usage.',
                'price' => '99.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 900,
                'durationInMonths' => 1,
            ],
            [
                'name' => 'Starter Annual',
                'description' => 'Annual subscription for small WooCommerce stores with two months included.',
                'price' => '190.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 1200,
                'durationInMonths' => 12,
            ],
            [
                'name' => 'Growth Annual',
                'description' => 'Annual subscription for growing stores with predictable virtual try-on usage.',
                'price' => '490.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 4200,
                'durationInMonths' => 12,
            ],
            [
                'name' => 'Scale Annual',
                'description' => 'Annual subscription for high-volume catalogs and advanced ecommerce teams.',
                'price' => '990.00',
                'type' => CreditSourceType::SUBSCRIPTION,
                'creditAmount' => 10800,
                'durationInMonths' => 12,
            ],
            [
                'name' => 'Free Trial Pack',
                'description' => 'Free one-time pack to test the virtual try-on workflow.',
                'price' => '0.00',
                'type' => CreditSourceType::ONE_TIME_PURCHASE,
                'creditAmount' => 10,
                'durationInMonths' => null,
            ],
            [
                'name' => 'Small Credit Pack',
                'description' => 'One-time credit pack for occasional virtual try-on generations.',
                'price' => '9.00',
                'type' => CreditSourceType::ONE_TIME_PURCHASE,
                'creditAmount' => 40,
                'durationInMonths' => null,
            ],
            [
                'name' => 'Medium Credit Pack',
                'description' => 'One-time credit pack for seasonal product campaigns.',
                'price' => '24.00',
                'type' => CreditSourceType::ONE_TIME_PURCHASE,
                'creditAmount' => 125,
                'durationInMonths' => null,
            ],
            [
                'name' => 'Large Credit Pack',
                'description' => 'One-time credit pack for larger catalogs and frequent tests.',
                'price' => '59.00',
                'type' => CreditSourceType::ONE_TIME_PURCHASE,
                'creditAmount' => 400,
                'durationInMonths' => null,
            ],
            [
                'name' => 'Enterprise Credit Pack',
                'description' => 'One-time credit pack for intensive virtual try-on usage.',
                'price' => '149.00',
                'type' => CreditSourceType::ONE_TIME_PURCHASE,
                'creditAmount' => 1200,
                'durationInMonths' => null,
            ],
        ];

        $createdOffers = [];

        foreach ($offers as $offerData) {
            $offer = (new Offer())
                ->setName($offerData['name'])
                ->setDescription($offerData['description'])
                ->setPrice($offerData['price'])
                ->setType($offerData['type'])
                ->setCreditAmount($offerData['creditAmount'])
                ->setDurationInMonths($offerData['durationInMonths']);

            $manager->persist($offer);
            $createdOffers[$offerData['name']] = $offer;
        }

        return $createdOffers;
    }

    /**
     * @param array<string, User>  $users
     * @param array<string, Offer> $offers
     */
    private function loadSubscriptions(ObjectManager $manager, array $users, array $offers): void
    {
        $subscriptionOffers = [
            'Starter Monthly',
            'Growth Monthly',
            'Scale Monthly',
            'Starter Annual',
            'Growth Annual',
            'Scale Annual',
        ];
        $oneTimeOffers = [
            'Free Trial Pack',
            'Small Credit Pack',
            'Medium Credit Pack',
            'Large Credit Pack',
            'Enterprise Credit Pack',
        ];
        for ($userIndex = 1; $userIndex <= 50; ++$userIndex) {
            $userEmail = sprintf('merchant%02d@example.test', $userIndex);
            $subscriptionCount = 1 + ($userIndex % 3);
            $activeSubscriptionIndex = 1 + (($userIndex - 1) % $subscriptionCount);

            for ($subscriptionIndex = 1; $subscriptionIndex <= $subscriptionCount; ++$subscriptionIndex) {
                $status = $subscriptionIndex === $activeSubscriptionIndex
                    ? Subscription::STATUS_ACTIVE
                    : (0 === ($userIndex + $subscriptionIndex) % 2 ? Subscription::STATUS_CANCELLED : Subscription::STATUS_EXPIRED);
                $offerName = $subscriptionOffers[($userIndex + $subscriptionIndex - 2) % count($subscriptionOffers)];
                $offer = $offers[$offerName];
                $startedDaysAgo = 8 + ($userIndex * 3) + ($subscriptionIndex * 17);
                $startsAt = new \DateTimeImmutable(sprintf('-%d days', $startedDaysAgo));
                $endsAt = $this->resolveSubscriptionEndDate($status, $startsAt, $offer->getDurationInMonths() ?? 1, $userIndex, $subscriptionIndex);

                $subscription = (new Subscription())
                    ->setUser($users[$userEmail])
                    ->setOffer($offer)
                    ->setStatus($status)
                    ->setStartsAt($startsAt)
                    ->setEndsAt($endsAt);

                $manager->persist($subscription);

                $this->createSubscriptionCreditBatch($manager, $subscription, $offer, $startsAt, $endsAt, $userIndex, $subscriptionIndex, $status);

                $extraPackCount = ($userIndex + $subscriptionIndex) % 4;

                for ($packIndex = 1; $packIndex <= $extraPackCount; ++$packIndex) {
                    $packOffer = $offers[$oneTimeOffers[($userIndex + $subscriptionIndex + $packIndex) % count($oneTimeOffers)]];
                    $this->createOneTimeCreditBatch($manager, $subscription, $packOffer, $startsAt, $userIndex, $subscriptionIndex, $packIndex);
                }
            }
        }
    }

    private function resolveSubscriptionEndDate(
        string $status,
        \DateTimeImmutable $startsAt,
        int $durationInMonths,
        int $userIndex,
        int $subscriptionIndex,
    ): ?\DateTimeImmutable {
        if (Subscription::STATUS_ACTIVE === $status) {
            return $startsAt->modify(sprintf('+%d months', $durationInMonths));
        }

        if (Subscription::STATUS_CANCELLED === $status) {
            return $startsAt->modify(sprintf('+%d days', 12 + (($userIndex + $subscriptionIndex) % 18)));
        }

        return $startsAt->modify(sprintf('+%d months', $durationInMonths));
    }

    private function createApiToken(
        ObjectManager $manager,
        User $user,
        int $userIndex,
    ): void {
        $apiToken = (new ApiToken())
            ->setUser($user)
            ->setTokenValue(sprintf('dm_test_user_%02d_%s', $userIndex, substr(hash('xxh32', $user->getEmail() ?? (string) $userIndex), 0, 12)))
            ->setIsActive(true)
            ->setActivatedAt(new \DateTimeImmutable(sprintf('-%d days', 4 + ($userIndex % 40))));

        $manager->persist($apiToken);
    }

    private function createSubscriptionCreditBatch(
        ObjectManager $manager,
        Subscription $subscription,
        Offer $offer,
        \DateTimeImmutable $startsAt,
        ?\DateTimeImmutable $endsAt,
        int $userIndex,
        int $subscriptionIndex,
        string $status,
    ): void {
        $initialAmount = $offer->getCreditAmount() ?? 0;
        $usageRate = 15 + (($userIndex * 7 + $subscriptionIndex * 11) % 78);
        $remainingAmount = max(0, $initialAmount - (int) round($initialAmount * $usageRate / 100));

        if (Subscription::STATUS_EXPIRED === $status) {
            $remainingAmount = min($remainingAmount, (int) floor($initialAmount * 0.12));
        }

        if (Subscription::STATUS_CANCELLED === $status) {
            $remainingAmount = min($remainingAmount, (int) floor($initialAmount * 0.35));
        }

        $creditBatch = (new CreditBatch())
            ->setSubscription($subscription)
            ->setType(CreditSourceType::SUBSCRIPTION)
            ->setInitialAmount($initialAmount)
            ->setRemainingAmount($remainingAmount)
            ->setExpiresAt($endsAt)
            ->setCreatedAt($startsAt->modify('+2 minutes'));

        $manager->persist($creditBatch);
    }

    private function createOneTimeCreditBatch(
        ObjectManager $manager,
        Subscription $subscription,
        Offer $offer,
        \DateTimeImmutable $startsAt,
        int $userIndex,
        int $subscriptionIndex,
        int $packIndex,
    ): void {
        $initialAmount = $offer->getCreditAmount() ?? 0;
        $usageRate = 5 + (($userIndex * 13 + $subscriptionIndex * 5 + $packIndex * 17) % 90);
        $remainingAmount = max(0, $initialAmount - (int) round($initialAmount * $usageRate / 100));
        $purchasedAt = $startsAt->modify(sprintf('+%d days', 1 + ($packIndex * 6) + ($userIndex % 9)));
        $expiresAt = 0 === $packIndex % 2 ? null : $purchasedAt->modify('+90 days');

        $creditBatch = (new CreditBatch())
            ->setSubscription($subscription)
            ->setType(CreditSourceType::ONE_TIME_PURCHASE)
            ->setInitialAmount($initialAmount)
            ->setRemainingAmount($remainingAmount)
            ->setExpiresAt($expiresAt)
            ->setCreatedAt($purchasedAt);

        $manager->persist($creditBatch);
    }
}
