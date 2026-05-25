<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class WordPressTryOnStatusRequest
{
    #[Assert\NotBlank(message: 'The api_key field is required.')]
    public ?string $apiKey = null;

    #[Assert\NotBlank(message: 'The site_url field is required.')]
    #[Assert\Url(message: 'The site_url field must be a valid URL.')]
    public ?string $siteUrl = null;

    #[Assert\NotBlank(message: 'The job_id field is required.')]
    #[Assert\Length(max: 64, maxMessage: 'The job_id field must not exceed {{ limit }} characters.')]
    public ?string $jobId = null;

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $request = new self();
        $request->apiKey = isset($payload['api_key']) ? trim((string) $payload['api_key']) : null;
        $request->siteUrl = isset($payload['site_url']) ? trim((string) $payload['site_url']) : null;
        $request->jobId = isset($payload['job_id']) ? trim((string) $payload['job_id']) : null;

        return $request;
    }
}
