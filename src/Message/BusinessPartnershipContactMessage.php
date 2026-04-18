<?php

namespace App\Message;

final readonly class BusinessPartnershipContactMessage
{
    public function __construct(
        public string $name,
        public string $email,
        public string $company,
        public ?string $website,
        public string $message,
    ) {
    }
}
