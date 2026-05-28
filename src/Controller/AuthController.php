<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\Auth\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    #[Route('/signup', name: 'app_signup', methods: ['GET', 'POST'])]
    public function signup(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
    ): Response {
        $error = null;
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setRoles([UserRole::USER])
                ->setPassword($passwordHasher->hashPassword($user, (string) $form->get('plainPassword')->getData()));

            try {
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $error = 'flash.auth.account_exists';
            }

            if (null === $error) {
                $loginResponse = $security->login($user, LoginFormAuthenticator::class, 'main');

                return $loginResponse ?? $this->redirectToRoute('middle_dashboard');
            }
        }

        return $this->render('front/register.html.twig', [
            'error' => $error,
            'registration_form' => $form,
        ]);
    }
}
