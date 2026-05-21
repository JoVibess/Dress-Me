<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class WordPressTryOnRequest
{
    #[Assert\NotBlank(message: 'The api_key field is required.')]
    public ?string $apiKey = null;

    #[Assert\NotBlank(message: 'The anonymous_visitor_id field is required.')]
    #[Assert\Length(max: 255, maxMessage: 'The anonymous_visitor_id field must not exceed {{ limit }} characters.')]
    public ?string $anonymousVisitorId = null;

    #[Assert\NotBlank(message: 'The site_url field is required.')]
    #[Assert\Url(message: 'The site_url field must be a valid URL.')]
    public ?string $siteUrl = null;

    #[Assert\PositiveOrZero(message: 'The anonymous_daily_quota field must be zero or a positive integer.')]
    public ?int $anonymousDailyQuota = null;

    #[Assert\NotNull(message: 'The product field is required.')]
    #[Assert\Valid]
    public ?WordPressProductPayload $product = null;

    public ?string $customerImage = null;

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $request = new self();
        $request->apiKey = isset($payload['api_key']) ? trim((string) $payload['api_key']) : null;
        $request->anonymousVisitorId = isset($payload['anonymous_visitor_id']) ? trim((string) $payload['anonymous_visitor_id']) : null;
        $request->siteUrl = isset($payload['site_url']) ? trim((string) $payload['site_url']) : null;
        $request->anonymousDailyQuota = isset($payload['anonymous_daily_quota']) ? (int) $payload['anonymous_daily_quota'] : null;
        $request->product = is_array($payload['product'] ?? null) ? WordPressProductPayload::fromArray($payload['product']) : null;
        $request->customerImage = isset($payload['customer_image']) && '' !== trim((string) $payload['customer_image'])
            ? (string) $payload['customer_image']
            : null;

        return $request;
    }
}
