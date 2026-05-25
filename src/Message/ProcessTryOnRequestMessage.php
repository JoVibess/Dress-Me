<?php

namespace App\Message;

final readonly class ProcessTryOnRequestMessage
{
    public function __construct(
        public int $tryOnRequestId,
    ) {
    }
}
