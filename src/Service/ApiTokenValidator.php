<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;

final class ApiTokenValidator
{
    public function __construct(private readonly ApiTokenRepository $apiTokenRepository)
    {
    }

    public function findValidStoreToken(string $apiKey, ?string $siteUrl = null): ?ApiToken
    {
        $apiToken = $this->apiTokenRepository->findActiveByTokenValue($apiKey);

        if (null === $apiToken) {
            return null;
        }

        $store = $apiToken->getStore();

        if (null === $store || !$store->isActive()) {
            return null;
        }

        if (null !== $siteUrl && !$this->matchesStoreWebsite($siteUrl, (string) $store->getWebsite())) {
            return null;
        }

        return $apiToken;
    }

    private function matchesStoreWebsite(string $siteUrl, string $storeWebsite): bool
    {
        $siteHost = $this->extractHost($siteUrl);
        $storeHost = $this->extractHost($storeWebsite);

        if (null !== $siteHost && null !== $storeHost) {
            return $siteHost === $storeHost;
        }

        return rtrim(strtolower($siteUrl), '/') === rtrim(strtolower($storeWebsite), '/');
    }

    private function extractHost(string $url): ?string
    {
        $host = parse_url($url, \PHP_URL_HOST);

        return \is_string($host) ? strtolower($host) : null;
    }
}
