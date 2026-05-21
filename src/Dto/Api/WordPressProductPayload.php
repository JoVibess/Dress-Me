<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class WordPressProductPayload
{
    #[Assert\NotNull(message: 'The product.id field is required.')]
    #[Assert\Positive(message: 'The product.id field must be a positive integer.')]
    public ?int $id = null;

    #[Assert\PositiveOrZero(message: 'The product.variation_id field must be zero or a positive integer.')]
    public ?int $variationId = null;

    #[Assert\NotBlank(message: 'The product.title field is required.')]
    #[Assert\Length(max: 255, maxMessage: 'The product.title field must not exceed {{ limit }} characters.')]
    public ?string $title = null;

    public ?string $description = null;

    #[Assert\Url(message: 'The product.image_url field must be a valid URL.')]
    public ?string $imageUrl = null;

    /**
     * @var string[]
     */
    public array $categories = [];

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $product = new self();
        $product->id = isset($payload['id']) ? (int) $payload['id'] : null;
        $product->variationId = isset($payload['variation_id']) ? (int) $payload['variation_id'] : null;
        $product->title = isset($payload['title']) ? trim((string) $payload['title']) : null;
        $product->description = isset($payload['description']) ? trim((string) $payload['description']) : null;
        $product->imageUrl = isset($payload['image_url']) && '' !== trim((string) $payload['image_url'])
            ? trim((string) $payload['image_url'])
            : null;
        $product->categories = array_values(array_filter(
            array_map(
                static fn (mixed $value): string => trim((string) $value),
                is_array($payload['categories'] ?? null) ? $payload['categories'] : [],
            ),
            static fn (string $value): bool => '' !== $value,
        ));

        return $product;
    }
}
