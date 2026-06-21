<?php

namespace App\MessageHandler;

use App\Message\CustomerSupportContactMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final readonly class CustomerSupportContactMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%env(MAILER_FROM_EMAIL)%')]
        private string $fromEmail,
        #[Autowire('%env(CONTACT_TO_EMAIL)%')]
        private string $contactToEmail,
    ) {
    }

    public function __invoke(CustomerSupportContactMessage $message): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($this->contactToEmail)
            ->replyTo($message->email)
            ->subject(sprintf('[DressMe Support] %s', $message->subject))
            ->text(sprintf(
                "New customer support request\n\nName: %s\nEmail: %s\nSubject: %s\n\nMessage:\n%s\n",
                $message->name,
                $message->email,
                $message->subject,
                $message->message,
            ));

        $this->mailer->send($email);
    }
}
