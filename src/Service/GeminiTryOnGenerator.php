<?php

namespace App\Service;

use App\Entity\TryOnRequest;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GeminiTryOnGenerator
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private TryOnImageStorage $tryOnImageStorage,
        #[Autowire('%env(GOOGLE_AI_STUDIO_API_KEY)%')]
        private string $apiKey,
    ) {
    }

    /**
     * @return array{provider_request_id: ?string, generated_image_path: string}
     */
    public function generate(TryOnRequest $tryOnRequest): array
    {
        if ('' === trim($this->apiKey)) {
            throw new \RuntimeException('GOOGLE_AI_STUDIO_API_KEY is not configured.');
        }

        $customerImagePath = $tryOnRequest->getCustomerImagePath();

        if (null === $customerImagePath) {
            throw new \RuntimeException('Customer image is missing for this try-on request.');
        }

        $customerImageDataUrl = $this->buildCustomerImageDataUrl($customerImagePath);
        $productImageDataUrl = $this->buildProductImageDataUrl($tryOnRequest);
        $providerRequestId = null;
        $lastErrorMessage = 'Gemini did not return a generated image.';

        foreach ($this->candidateModels() as $model) {
            $response = $this->httpClient->request(
                'POST',
                sprintf('https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent', $model),
                [
                    'headers' => [
                        'x-goog-api-key' => $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'contents' => [[
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $this->buildPrompt($tryOnRequest),
                                ],
                                [
                                    'inline_data' => $this->buildInlineDataPart($customerImageDataUrl),
                                ],
                                [
                                    'inline_data' => $this->buildInlineDataPart($productImageDataUrl),
                                ],
                            ],
                        ]],
                        'generationConfig' => [
                            'responseModalities' => ['TEXT', 'IMAGE'],
                        ],
                    ],
                    'timeout' => 180,
                ],
            );

            $payload = $response->toArray(false);
            $providerRequestId = is_string($payload['responseId'] ?? null)
                ? $payload['responseId']
                : (is_string($payload['id'] ?? null) ? $payload['id'] : null);
            $imageData = $this->extractGeneratedImageData($payload);

            if (null !== $imageData) {
                $binary = base64_decode($imageData, true);

                if (false === $binary) {
                    throw new \RuntimeException('Unable to decode generated image returned by Gemini.');
                }

                $generatedImagePath = $this->tryOnImageStorage->storeGeneratedImage($tryOnRequest->getJobId() ?? 'tryon', $binary);

                return [
                    'provider_request_id' => $providerRequestId ?? $model,
                    'generated_image_path' => $generatedImagePath,
                ];
            }

            $lastErrorMessage = $this->extractErrorMessage($payload, $model);
        }

        throw new \RuntimeException($lastErrorMessage);
    }

    private function buildCustomerImageDataUrl(string $relativePath): string
    {
        $binary = $this->tryOnImageStorage->readBinary($relativePath);
        $mimeType = $this->tryOnImageStorage->guessMimeTypeFromPath($relativePath);

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($binary));
    }

    private function buildProductImageDataUrl(TryOnRequest $tryOnRequest): string
    {
        $productImageUrl = $tryOnRequest->getProductImageUrl();

        if (null === $productImageUrl || '' === trim($productImageUrl)) {
            throw new \RuntimeException('Product image URL is missing for this try-on request.');
        }

        $response = $this->httpClient->request('GET', $productImageUrl, [
            'timeout' => 30,
        ]);
        $binary = $response->getContent();
        $mimeType = $response->getHeaders(false)['content-type'][0] ?? 'image/jpeg';
        $mimeType = strtolower(trim(explode(';', $mimeType)[0]));

        if (!str_starts_with($mimeType, 'image/')) {
            $mimeType = 'image/jpeg';
        }

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($binary));
    }

    private function buildPrompt(TryOnRequest $tryOnRequest): string
    {
        $productTitle = $tryOnRequest->getProductTitle() ?? 'garment';
        $description = trim((string) $tryOnRequest->getProductDescription());

        $parts = [
            'Use the first image as the customer photo and the second image as the garment reference.',
            'Generate one photorealistic ecommerce-quality virtual try-on image.',
            'Keep the customer identity, body proportions, pose, and overall scene as consistent as possible.',
            sprintf('Dress the customer with the product "%s".', $productTitle),
            'Preserve the garment shape, fit, color, fabric, and visual details from the reference product image.',
            'Do not add extra garments, accessories, or text overlays.',
            'Output only the final try-on image.',
        ];

        if ('' !== $description) {
            $parts[] = sprintf('Product description for styling context: %s', $description);
        }

        return implode(' ', $parts);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return string|null
     */
    private function extractGeneratedImageData(array $payload): ?string
    {
        $candidates = $payload['candidates'] ?? null;

        if (!is_array($candidates)) {
            return null;
        }

        foreach ($candidates as $candidate) {
            $parts = $candidate['content']['parts'] ?? null;

            if (!is_array($parts)) {
                continue;
            }

            foreach ($parts as $part) {
                $inlineData = $part['inlineData'] ?? $part['inline_data'] ?? null;

                if (
                    is_array($inlineData)
                    && is_string($inlineData['data'] ?? null)
                    && '' !== $inlineData['data']
                ) {
                    return $inlineData['data'];
                }
            }
        }

        return null;
    }

    /**
     * @return array{mime_type: string, data: string}
     */
    private function buildInlineDataPart(string $dataUrl): array
    {
        if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $dataUrl, $matches)) {
            throw new \RuntimeException('Unsupported image payload for Gemini.');
        }

        return [
            'mime_type' => $matches[1],
            'data' => $matches[2],
        ];
    }

    /**
     * @return string[]
     */
    private function candidateModels(): array
    {
        return [
            'gemini-3-pro-image-preview',
            'gemini-2.5-flash-image',
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractErrorMessage(array $payload, string $model): string
    {
        $apiMessage = $payload['error']['message'] ?? null;

        if (is_string($apiMessage) && '' !== trim($apiMessage)) {
            return sprintf('%s failed: %s', $model, $apiMessage);
        }

        return sprintf('%s did not return a generated image.', $model);
    }
}
