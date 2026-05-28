<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class LanguageController extends AbstractController
{
    #[Route('/language/{locale}', name: 'app_switch_locale', requirements: ['locale' => 'en|fr'], methods: ['GET'])]
    public function switch(string $locale, Request $request, SessionInterface $session): RedirectResponse
    {
        $session->set('_locale', $locale);

        $redirect = (string) $request->query->get('redirect', '');

        if ('' === $redirect || !str_starts_with($redirect, '/')) {
            return $this->redirectToRoute('front_home');
        }

        return $this->redirect($redirect);
    }
}
