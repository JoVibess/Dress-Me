<?php

namespace App\Message;

final readonly class CustomerSupportContactMessage
{
    public function __construct(
        public string $name,
        public string $email,
        public string $subject,
        public string $message,
    ) {
    }
}
