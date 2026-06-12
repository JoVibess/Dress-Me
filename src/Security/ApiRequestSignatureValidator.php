<?php

namespace App\Security;

use App\Entity\ApiToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class ApiRequestSignatureValidator
{
    private const HEADER_API_KEY = 'X-DressMe-Key';
    private const HEADER_NONCE = 'X-DressMe-Nonce';
    private const HEADER_SIGNATURE = 'X-DressMe-Signature';
    private const HEADER_TIMESTAMP = 'X-DressMe-Timestamp';

    public function __construct(
        #[Autowire(service: 'app.api_request_nonce_cache')]
        private CacheInterface $nonceCache,
        #[Autowire(param: 'app.api_request_auth.max_age_seconds')]
        private int $maxRequestAgeSeconds,
        #[Autowire(param: 'app.api_request_auth.nonce_ttl_seconds')]
        private int $nonceTtlSeconds,
    ) {
    }

    public function assertValid(Request $request, ApiToken $apiToken): void
    {
        $secret = trim((string) $apiToken->getSecretValue());

        if ('' === $secret) {
            throw new ApiRequestAuthenticationException(
                'API_SECRET_NOT_CONFIGURED',
                401,
                'The API secret is not configured for this store.',
            );
        }

        $headerApiKey = trim((string) $request->headers->get(self::HEADER_API_KEY, ''));
        $timestamp = trim((string) $request->headers->get(self::HEADER_TIMESTAMP, ''));
        $nonce = trim((string) $request->headers->get(self::HEADER_NONCE, ''));
        $providedSignature = trim((string) $request->headers->get(self::HEADER_SIGNATURE, ''));

        if ('' === $headerApiKey || '' === $timestamp || '' === $nonce || '' === $providedSignature) {
            throw new ApiRequestAuthenticationException(
                'MISSING_AUTH_HEADERS',
                401,
                'Required authentication headers are missing.',
            );
        }

        if ($headerApiKey !== $apiToken->getTokenValue()) {
            throw new ApiRequestAuthenticationException(
                'INVALID_API_KEY',
                401,
                'The provided API key is invalid for this store.',
            );
        }

        if (!ctype_digit($timestamp)) {
            throw new ApiRequestAuthenticationException(
                'INVALID_TIMESTAMP',
                401,
                'The request timestamp is invalid.',
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{16,255}$/', $nonce)) {
            throw new ApiRequestAuthenticationException(
                'INVALID_NONCE',
                401,
                'The request nonce is invalid.',
            );
        }

        if (!preg_match('/^[a-f0-9]{64}$/i', $providedSignature)) {
            throw new ApiRequestAuthenticationException(
                'INVALID_SIGNATURE',
                401,
                'The request signature format is invalid.',
            );
        }

        $requestTimestamp = (int) $timestamp;
        $now = time();

        if (abs($now - $requestTimestamp) > $this->maxRequestAgeSeconds) {
            throw new ApiRequestAuthenticationException(
                'REQUEST_EXPIRED',
                401,
                'The request timestamp is outside the accepted window.',
            );
        }

        $expectedSignature = hash_hmac('sha256', $this->buildStringToSign($request, $timestamp, $nonce), $secret);

        if (!hash_equals($expectedSignature, strtolower($providedSignature))) {
            @file_put_contents(
                '/tmp/dressme-hmac.log',
                sprintf(
                    "[%s] Invalid signature. key=%s path_info=%s request_uri=%s method=%s timestamp=%s nonce=%s body_hash=%s expected=%s provided=%s body=%s\n",
                    date(DATE_ATOM),
                    $headerApiKey,
                    $request->getPathInfo(),
                    $request->getRequestUri(),
                    strtoupper($request->getMethod()),
                    $timestamp,
                    $nonce,
                    hash('sha256', $request->getContent()),
                    $expectedSignature,
                    strtolower($providedSignature),
                    $request->getContent(),
                ),
                FILE_APPEND,
            );
            throw new ApiRequestAuthenticationException(
                'INVALID_SIGNATURE',
                401,
                'The request signature is invalid.',
            );
        }

        $this->guardAgainstNonceReplay($apiToken, $nonce);
    }

    public function hasAuthenticationHeaders(Request $request): bool
    {
        return '' !== trim((string) $request->headers->get(self::HEADER_API_KEY, ''))
            || '' !== trim((string) $request->headers->get(self::HEADER_TIMESTAMP, ''))
            || '' !== trim((string) $request->headers->get(self::HEADER_NONCE, ''))
            || '' !== trim((string) $request->headers->get(self::HEADER_SIGNATURE, ''));
    }

    private function buildStringToSign(Request $request, string $timestamp, string $nonce): string
    {
        $bodyHash = hash('sha256', $request->getContent());

        return implode("\n", [
            strtoupper($request->getMethod()),
            $request->getPathInfo(),
            $timestamp,
            $nonce,
            $bodyHash,
        ]);
    }

    private function guardAgainstNonceReplay(ApiToken $apiToken, string $nonce): void
    {
        $cacheKey = sprintf('api_nonce_%d_%s', (int) $apiToken->getId(), hash('sha256', $nonce));
        $wasCreated = false;

        $this->nonceCache->get($cacheKey, function (ItemInterface $item) use (&$wasCreated): bool {
            $wasCreated = true;
            $item->expiresAfter($this->nonceTtlSeconds);

            return true;
        });

        if (!$wasCreated) {
            throw new ApiRequestAuthenticationException(
                'NONCE_ALREADY_USED',
                401,
                'The request nonce has already been used.',
            );
        }
    }
}
