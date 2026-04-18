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
        $users = [
            [
                'name' => 'Admin DressMe',
                'email' => 'admin@dressme.test',
                'website' => 'https://admin.dressme.test',
                'roles' => [UserRole::ADMIN],
                'plainPassword' => 'admin123',
            ],
            [
                'name' => 'Alice Martin',
                'email' => 'alice@example.test',
                'website' => 'https://alice-shop.test',
                'roles' => [UserRole::USER],
                'plainPassword' => 'user123',
            ],
            [
                'name' => 'Ben Carter',
                'email' => 'ben@example.test',
                'website' => 'https://ben-store.test',
                'roles' => [UserRole::USER],
                'plainPassword' => 'user123',
            ],
            [
                'name' => 'Chloe Nguyen',
                'email' => 'chloe@example.test',
                'website' => 'https://chloe-fashion.test',
                'roles' => [UserRole::USER],
                'plainPassword' => 'user123',
            ],
            [
                'name' => 'David Lopez',
                'email' => 'david@example.test',
                'website' => 'https://david-boutique.test',
                'roles' => [UserRole::USER],
                'plainPassword' => 'user123',
            ],
        ];

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
        }

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

        $subscriptions = [
            [
                'userEmail' => 'alice@example.test',
                'offerName' => 'Starter Monthly',
                'status' => Subscription::STATUS_ACTIVE,
                'startsAt' => '-10 days',
                'endsAt' => '+20 days',
                'tokenValue' => 'dm_test_alice_starter_001',
                'tokenActive' => true,
                'creditBatches' => [
                    ['type' => CreditSourceType::SUBSCRIPTION, 'initialAmount' => 100, 'remainingAmount' => 72, 'expiresAt' => '+20 days'],
                    ['type' => CreditSourceType::ONE_TIME_PURCHASE, 'initialAmount' => 40, 'remainingAmount' => 18, 'expiresAt' => null],
                ],
            ],
            [
                'userEmail' => 'ben@example.test',
                'offerName' => 'Growth Monthly',
                'status' => Subscription::STATUS_ACTIVE,
                'startsAt' => '-5 days',
                'endsAt' => '+25 days',
                'tokenValue' => 'dm_test_ben_growth_001',
                'tokenActive' => true,
                'creditBatches' => [
                    ['type' => CreditSourceType::SUBSCRIPTION, 'initialAmount' => 350, 'remainingAmount' => 301, 'expiresAt' => '+25 days'],
                ],
            ],
            [
                'userEmail' => 'chloe@example.test',
                'offerName' => 'Scale Monthly',
                'status' => Subscription::STATUS_ACTIVE,
                'startsAt' => '-20 days',
                'endsAt' => '+10 days',
                'tokenValue' => 'dm_test_chloe_scale_001',
                'tokenActive' => true,
                'creditBatches' => [
                    ['type' => CreditSourceType::SUBSCRIPTION, 'initialAmount' => 900, 'remainingAmount' => 522, 'expiresAt' => '+10 days'],
                    ['type' => CreditSourceType::ONE_TIME_PURCHASE, 'initialAmount' => 125, 'remainingAmount' => 125, 'expiresAt' => null],
                ],
            ],
            [
                'userEmail' => 'david@example.test',
                'offerName' => 'Starter Monthly',
                'status' => Subscription::STATUS_CANCELLED,
                'startsAt' => '-70 days',
                'endsAt' => '-40 days',
                'tokenValue' => 'dm_test_david_cancelled_001',
                'tokenActive' => false,
                'creditBatches' => [
                    ['type' => CreditSourceType::SUBSCRIPTION, 'initialAmount' => 100, 'remainingAmount' => 0, 'expiresAt' => '-40 days'],
                ],
            ],
        ];

        foreach ($subscriptions as $subscriptionData) {
            $subscription = (new Subscription())
                ->setUser($createdUsers[$subscriptionData['userEmail']])
                ->setOffer($createdOffers[$subscriptionData['offerName']])
                ->setStatus($subscriptionData['status'])
                ->setStartsAt(new \DateTimeImmutable($subscriptionData['startsAt']))
                ->setEndsAt(new \DateTimeImmutable($subscriptionData['endsAt']));

            $manager->persist($subscription);

            $apiToken = (new ApiToken())
                ->setSubscription($subscription)
                ->setTokenValue($subscriptionData['tokenValue'])
                ->setIsActive($subscriptionData['tokenActive'])
                ->setActivatedAt($subscriptionData['tokenActive'] ? new \DateTimeImmutable($subscriptionData['startsAt']) : null);

            $manager->persist($apiToken);

            foreach ($subscriptionData['creditBatches'] as $creditBatchData) {
                $creditBatch = (new CreditBatch())
                    ->setSubscription($subscription)
                    ->setType($creditBatchData['type'])
                    ->setInitialAmount($creditBatchData['initialAmount'])
                    ->setRemainingAmount($creditBatchData['remainingAmount'])
                    ->setExpiresAt(null === $creditBatchData['expiresAt'] ? null : new \DateTimeImmutable($creditBatchData['expiresAt']));

                $manager->persist($creditBatch);
            }
        }

        $manager->flush();
    }
}
