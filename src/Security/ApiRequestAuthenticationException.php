<?php

namespace App\Security;

final class ApiRequestAuthenticationException extends \RuntimeException
{
    public function __construct(
        private readonly string $errorCode,
        private readonly int $statusCode,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
