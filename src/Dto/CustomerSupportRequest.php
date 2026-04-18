<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CustomerSupportRequest
{
    public string $contactType = 'customer_support';

    #[Assert\NotBlank(message: 'Please enter your name.')]
    #[Assert\Length(max: 120)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Please enter your email.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Please enter a subject.')]
    #[Assert\Length(max: 180)]
    public ?string $subject = null;

    #[Assert\NotBlank(message: 'Please enter your message.')]
    #[Assert\Length(min: 10, max: 3000)]
    public ?string $message = null;
}
