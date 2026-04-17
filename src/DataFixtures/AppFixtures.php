<?php

namespace App\DataFixtures;

use App\Entity\Offer;
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

        foreach ($users as $userData) {
            $user = (new User())
                ->setName($userData['name'])
                ->setEmail($userData['email'])
                ->setWebsite($userData['website'])
                ->setRoles($userData['roles']);

            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['plainPassword']));

            $manager->persist($user);
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

        foreach ($offers as $offerData) {
            $offer = (new Offer())
                ->setName($offerData['name'])
                ->setDescription($offerData['description'])
                ->setPrice($offerData['price'])
                ->setType($offerData['type'])
                ->setCreditAmount($offerData['creditAmount'])
                ->setDurationInMonths($offerData['durationInMonths']);

            $manager->persist($offer);
        }

        $manager->flush();
    }
}
