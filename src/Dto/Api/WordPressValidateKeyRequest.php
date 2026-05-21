<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class WordPressValidateKeyRequest
{
    #[Assert\NotBlank(message: 'The api_key field is required.')]
    public ?string $apiKey = null;

    #[Assert\Url(message: 'The site_url field must be a valid URL.')]
    public ?string $siteUrl = null;

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $request = new self();
        $request->apiKey = isset($payload['api_key']) ? trim((string) $payload['api_key']) : null;
        $request->siteUrl = isset($payload['site_url']) && '' !== trim((string) $payload['site_url'])
            ? trim((string) $payload['site_url'])
            : null;

        return $request;
    }
}
