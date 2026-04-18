<?php

namespace App\Controller\Front;

use App\Dto\BusinessPartnershipRequest;
use App\Dto\CustomerSupportRequest;
use App\Form\Contact\BusinessPartnershipFormType;
use App\Form\Contact\CustomerSupportFormType;
use App\Message\BusinessPartnershipContactMessage;
use App\Message\CustomerSupportContactMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'front_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/features', name: 'front_features', methods: ['GET'])]
    public function features(): Response
    {
        return $this->render('front/features.html.twig');
    }

    #[Route('/contact', name: 'front_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MessageBusInterface $messageBus): Response
    {
        $customerSupportRequest = new CustomerSupportRequest();
        $businessPartnershipRequest = new BusinessPartnershipRequest();
        $customerSupportForm = $this->createForm(CustomerSupportFormType::class, $customerSupportRequest);
        $businessPartnershipForm = $this->createForm(BusinessPartnershipFormType::class, $businessPartnershipRequest);

        $customerSupportForm->handleRequest($request);
        $businessPartnershipForm->handleRequest($request);

        $activeContactForm = $businessPartnershipForm->isSubmitted() ? 'partnership' : 'support';

        if ($customerSupportForm->isSubmitted() && $customerSupportForm->isValid()) {
            $messageBus->dispatch(new CustomerSupportContactMessage(
                (string) $customerSupportRequest->name,
                (string) $customerSupportRequest->email,
                (string) $customerSupportRequest->subject,
                (string) $customerSupportRequest->message,
            ));

            $this->addFlash('success', 'Your support request has been sent.');

            return $this->redirectToRoute('front_contact');
        }

        if ($businessPartnershipForm->isSubmitted() && $businessPartnershipForm->isValid()) {
            $messageBus->dispatch(new BusinessPartnershipContactMessage(
                (string) $businessPartnershipRequest->name,
                (string) $businessPartnershipRequest->email,
                (string) $businessPartnershipRequest->company,
                $businessPartnershipRequest->website,
                (string) $businessPartnershipRequest->message,
            ));

            $this->addFlash('success', 'Your partnership request has been sent.');

            return $this->redirectToRoute('front_contact');
        }

        return $this->render('front/contact.html.twig', [
            'customer_support_form' => $customerSupportForm,
            'business_partnership_form' => $businessPartnershipForm,
            'active_contact_form' => $activeContactForm,
        ]);
    }
}
