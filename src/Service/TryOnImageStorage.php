<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class TryOnImageStorage
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
    ) {
    }

    public function storeCustomerImage(string $jobId, string $dataUri): string
    {
        [$mimeType, $binary] = $this->decodeDataUri($dataUri);
        $extension = $this->guessExtensionFromMimeType($mimeType);
        $relativePath = sprintf('uploads/try-on/customer/%s.%s', $jobId, $extension);

        $this->writeBinary($relativePath, $binary);

        return $relativePath;
    }

    public function storeGeneratedImage(string $jobId, string $binary): string
    {
        $mimeType = $this->detectMimeType($binary);
        $extension = $this->guessExtensionFromMimeType($mimeType);
        $relativePath = sprintf('uploads/try-on/generated/%s.%s', $jobId, $extension);

        $this->writeBinary($relativePath, $binary);

        return $relativePath;
    }

    public function readBinary(string $relativePath): string
    {
        $absolutePath = $this->absolutePath($relativePath);

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            throw new \RuntimeException(sprintf('Try-on asset not found: %s', $relativePath));
        }

        $binary = file_get_contents($absolutePath);

        if (false === $binary) {
            throw new \RuntimeException(sprintf('Unable to read try-on asset: %s', $relativePath));
        }

        return $binary;
    }

    public function guessMimeTypeFromPath(string $relativePath): string
    {
        $absolutePath = $this->absolutePath($relativePath);
        $mimeType = is_file($absolutePath) ? mime_content_type($absolutePath) : false;

        return is_string($mimeType) && '' !== $mimeType ? $mimeType : 'image/png';
    }

    private function writeBinary(string $relativePath, string $binary): void
    {
        $absolutePath = $this->absolutePath($relativePath);
        $directory = dirname($absolutePath);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create directory: %s', $directory));
        }

        if (false === file_put_contents($absolutePath, $binary)) {
            throw new \RuntimeException(sprintf('Unable to write try-on asset: %s', $relativePath));
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function decodeDataUri(string $dataUri): array
    {
        if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $dataUri, $matches)) {
            throw new \RuntimeException('Unsupported customer image payload.');
        }

        $binary = base64_decode($matches[2], true);

        if (false === $binary) {
            throw new \RuntimeException('Unable to decode customer image payload.');
        }

        return [$matches[1], $binary];
    }

    private function detectMimeType(string $binary): string
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($binary);

        return is_string($mimeType) && str_starts_with($mimeType, 'image/') ? $mimeType : 'image/png';
    }

    private function guessExtensionFromMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
    }

    private function absolutePath(string $relativePath): string
    {
        return sprintf('%s/public/%s', rtrim($this->projectDir, '/'), ltrim($relativePath, '/'));
    }
}
