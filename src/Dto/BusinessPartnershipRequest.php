<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BusinessPartnershipRequest
{
    public string $contactType = 'business_partnership';

    #[Assert\NotBlank(message: 'Please enter your name.')]
    #[Assert\Length(max: 120)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Please enter your work email.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Please enter your company.')]
    #[Assert\Length(max: 160)]
    public ?string $company = null;

    #[Assert\Url(message: 'Please enter a valid website URL.')]
    public ?string $website = null;

    #[Assert\NotBlank(message: 'Please describe your partnership goal.')]
    #[Assert\Length(min: 10, max: 3000)]
    public ?string $message = null;
}
